module.exports = function () {
  const [target, container, sidebar] = helper(['target', 'container', 'sidebar']);

  title(_('Public Marks'));

  const params = request_marks_params();
  let matches;

  if (url_is('/marks')) {
    container.marks(() => model('marks').latests(params));
    sidebar.register(['Public', 'Tags'], function () {
      return partial('tags', { tags: () => model('tags').latests() });
    });
    sidebar.register(['Active', 'Users'], function () {
      return partial('users', { users: helper('related').active_users });
    });
    return render('marks');
  } else if ((matches = url_match('/marks/tag/*'))) {
    let tags = matches[1].split(',');
    const tag = target.tag(tags[0]);
    if (tags.length === 1) {
      title(_('Public Marks'), 'with tag ' + strong(tag));
      container.marks(() => model('marks').with_tag(tag, params));
    } else {
      tags = tags.map((slug) => table('tags').get_one('label', decodeURIComponent(slug)));
      const labels = tags.map((tagItem) => strong(tagItem));
      title(_('Public Marks'), 'with tags ' + labels.join(' &amp; '));
      container.marks(() => model('marks').with_tags(tags, params));
    }
    container.tags(() => model('tags').related_with(tag));
    sidebar.register(['Tags', 'related with ' + strong(tag)], function () {
      return partial('tags');
    });
    sidebar.register(['Active', 'Users with tag ' + strong(tag)], function () {
      return partial('users', { users: helper('related').active_users });
    });
    return render('marks');
  } else if ((matches = url_match('/user/*/marks/tag/*'))) {
    const user = target.user(matches[1]);
    let tags = matches[2].split(',');
    const tag = target.tag(tags[0]);
    if (tags.length === 1) {
      title(_('Public Marks'), 'from ' + strong(user) + ' with tag ' + strong(tag));
      container.marks(() => model('marks').from_user_with_tag(user, tag, params));
    } else {
      tags = tags.map((slug) => table('tags').get_one('label', decodeURIComponent(slug)));
      const labels = tags.map((tagItem) => strong(tagItem));
      title(_('Public Marks'), 'from ' + strong(user) + 'with tags ' + labels.join(' &amp; '));
      container.marks(() => model('marks').from_user_with_tags(user, tags, params));
    }
    container.tags(() => model('tags').from_user_related_with(user, tag));
    sidebar.register(['Tags', 'from ' + strong(user) + ' related with ' + strong(tag)], function () {
      return partial('tags');
    });
    return render('marks');
  } else if ((matches = url_match('/user/*/marks'))) {
    const user = target.user(matches[1]);
    title(_('Public Marks'), 'from ' + strong(user));
    container.tags(() => model('tags').from_user(user));
    container.marks(() => model('marks').from_user(user, params));
    sidebar.register(['Tags', 'from ' + strong(user)], function () {
      return partial('tags');
    });
    return render('marks');
  } else if (url_is('/marks/search')) {
    const query = get_param('query');
    return query ? redirect('/marks/search/' + query) : redirect('/marks');
  } else if ((matches = url_match('/marks/search/*'))) {
    const query = set_param('query', decodeURIComponent(matches[1]));
    title(_('Public Marks'), 'with search ' + strong(query));
    container.marks(() => model('marks').public_search(query, params));
    container.tags(() => model('tags').public_search({ query }));
    sidebar.register(['Public', 'Tags with search ' + strong(query)], function () {
      return partial('tags');
    });
    return render('marks');
  } else if (url_start_with('/tag/')) {
    return redirect('/marks' + request_url());
  } else if (url_match('/user/*')) {
    return redirect(request_url() + '/marks');
  }

  return unknown_url();
};
