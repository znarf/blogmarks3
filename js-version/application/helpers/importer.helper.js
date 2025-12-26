const importer = anonymous_class();

const amqp = service('amqp');
const redis = service('redis').connection();

const [marks, links, tags, marks_tags, screenshots] = table([
  'marks',
  'links',
  'tags',
  'marks_tags',
  'screenshots'
]);

importer.start = function (user) {
  set_time_limit(0);
  this.user = user;
  const link_ids = marks.select('related as id').where({ author: user.id }).fetch_ids();
  this.reverse_link_ids = Object.fromEntries(link_ids.map((id) => [id, true]));
  this.amqp_channel = amqp.channel();
  if (this.amqp_channel) {
    this.amqp_channel.queue_declare('marks-index', false, true, false, false);
  }
  this.tag_ids = [];
  return this;
};

importer.preload = function (marks_params) {
  const hrefs = marks_params.map((params) => params.related);
  links.preload('href', hrefs);
  links.get_all('href', hrefs);
};

importer.parse = function (file) {
  const marks_params = [];
  const sxe = this.simplexml(file);
  for (const entry of sxe.entry) {
    marks_params.push(this.to_array(entry));
  }
  this.preload(marks_params);
  return marks_params;
};

importer.simplexml = function (file) {
  if (
    ['application/x-gzip', 'application/x-download', 'application/x-tar'].includes(
      FILES.file.type
    )
  ) {
    let xml = '';
    const handle = gzopen(file, 'r');
    let buffer;
    while ((buffer = gzread(handle, 4096))) {
      xml += buffer;
    }
    gzclose(handle);
    return simplexml_load_string(xml);
  }
  return simplexml_load_file(file);
};

importer.to_array = function (entry) {
  const params = {};

  params.title = String(entry.title);
  params.updated = String(entry.updated);
  params.published = String(entry.published);

  const bm = entry.children('http://blogmarks.net/ns/') || entry.children('https://blogmarks.net/ns/');
  if (bm.isPrivate && String(bm.isPrivate)) {
    params.visibility = 1;
  } else {
    params.visibility = 0;
  }

  params.image = null;
  params.related = null;
  params.via = null;
  for (const link of entry.link) {
    if (link.rel === 'related') {
      params.related = String(link.href);
    } else if (link.rel === 'via') {
      params.via = String(link.href);
    } else if (link.rel === 'enclosure') {
      if (link.type === 'image/png' || link.type === 'image/jpg') {
        params.image = String(link.href);
      }
    }
  }

  params.tags = [];
  params.private_tags = [];
  for (const category of entry.category) {
    const scheme = String(category.scheme);
    if (['http://blogmarks.net/tag/', 'https://blogmarks.net/tag/'].includes(scheme)) {
      params.tags.push(String(category.label));
    } else {
      params.private_tags.push(String(category.label));
    }
  }

  params.content = null;
  params.contentType = 'text';
  if (entry.content) {
    params.content = String(entry.content);
    params.contentType = String(entry.content.type);
  }

  return params;
};

importer.insert = function (params) {
  const link = links.with_url(params.related);
  if (this.reverse_link_ids[link.id]) {
    throw new exception('Already in your marks', 511);
  }
  if (params.published === params.updated) {
    params.published = this.convert_date(params.published);
    params.updated = this.convert_date(params.updated);
  } else {
    params.published = this.convert_date(params.published);
    params.updated = this.convert_date(params.updated);
  }
  if (params.image) {
    this.insert_screenshot(link, params.image, params.published);
  }
  const mark_id = this.insert_mark(this.user.id, link.id, params);
  this.insert_tags(mark_id, this.user.id, link.id, params);
  if (this.amqp_channel) {
    this.index(mark_id);
  }
  return mark_id;
};

importer.insert_screenshot = function (link, url, published) {
  const migrated_url = String(url).replace('http://blogmarks.net/', 'https://blogmarks.net/');
  if (!screenshots.get_one('link', link.id)) {
    screenshots
      .insert()
      .set({
        link: parseInt(link.id, 10),
        url: migrated_url,
        created: published,
        generated: published,
        status: 1
      })
      .execute();
  }
};

importer.insert_mark = function (user_id, link_id, params) {
  marks
    .insert()
    .set({
      title: params.title,
      contentType: params.contentType,
      content: params.content,
      author: parseInt(user_id, 10),
      related: parseInt(link_id, 10),
      visibility: params.visibility,
      published: params.published,
      updated: params.updated
    })
    .execute();
  return amateur.model.db.insert_id();
};

importer.insert_tags = function (mark_id, user_id, link_id, params) {
  const marks_tags_query = marks_tags.insert([
    'mark_id',
    'tag_id',
    'user_id',
    'link_id',
    'label',
    'isHidden',
    'visibility'
  ]);
  const tag_sets = { tags: 0, private_tags: 1 };
  for (const [key, isHidden] of Object.entries(tag_sets)) {
    for (const tagValue of params[key]) {
      const tag = tags.with_label(tagValue);
      this.tag_ids.push(tag.id);
      marks_tags_query.values.push([
        parseInt(mark_id, 10),
        parseInt(tag.id, 10),
        parseInt(user_id, 10),
        parseInt(link_id, 10),
        tag.label,
        isHidden,
        params.visibility
      ]);
      if (amateur.model.db.driver() === 'sqlite') {
        marks_tags_query.execute();
        marks_tags_query.values = [];
      }
    }
  }
  if (marks_tags_query.values.length) {
    marks_tags_query.execute();
  }
};

importer.index = function (mark_id) {
  if (!this._count) {
    this._count = 0;
  }
  const message = amqp.json_message({ action: 'index', mark_id });
  this.amqp_channel.batch_basic_publish(message, '', 'marks-index');
  this._count += 1;
  if (this._count % 1000 === 0) {
    this.amqp_channel.publish_batch();
  }
};

importer.finish = function () {
  if (this.amqp_channel) {
    this.amqp_channel.publish_batch();
  }
  if (redis) {
    redis.delete('feed_marks');
    redis.delete(`feed_marks_my_${this.user.id}`);
    redis.delete(`feed_marks_user_${this.user.id}`);
    for (const tag_id of Array.from(new Set(this.tag_ids))) {
      redis.delete(`feed_marks_tag_${tag_id}`);
      redis.delete(`feed_marks_my_${this.user.id}_with_tag_${tag_id}`);
    }
    redis.delete('tags_public');
    redis.delete(`tags_user_${this.user.id}_public`);
    redis.delete(`tags_user_${this.user.id}_private`);
    if (flag('enable_social_features')) {
      for (const user_id of this.user.follower_ids) {
        redis.delete(`feed_marks_friends_${user_id}}`);
      }
    }
  }
};

importer.convert_date = function (string) {
  return amateur.model.db.date(string);
};

module.exports = importer;
