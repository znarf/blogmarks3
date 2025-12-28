module.exports = function () {
  const [target, container, sidebar] = helper(['target', 'container', 'sidebar']);

  domain('my');
  section('my');
  title(_('My Marks'));

  check_authenticated();
  const user = authenticated_user();

  const params = request_marks_params();
  let matches;

  if (url_is('/my/marks')) {
    container.marks(() => model('marks').private_from_user(user, params));
    container.tags(() => model('tags').private_from_user(user));
    sidebar.register(['My', 'Tags'], function () {
      return partial('tags');
    });
    return render('marks');
  } else if ((matches = url_match('/my/marks/tag/*'))) {
    let tags = matches[1].split(',');
    const tag = target.tag(tags[0]);
    if (tags.length === 1) {
      title(_('My Marks'), _('with tag') + ' ' + strong(tag));
      container.marks(() => model('marks').private_from_user_with_tag(user, tag, params));
    } else {
      tags = tags.map((slug) => table('tags').get_one('label', decodeURIComponent(slug)));
      const labels = tags.map((tagItem) => strong(tagItem));
      title(_('My Marks'), 'with tags ' + labels.join(' &amp; '));
      container.marks(() => model('marks').private_from_user_with_tags(user, tags, params));
    }
    container.tags(() => model('tags').private_from_user_related_with(user, tag));
    sidebar.register(['My', 'Tags related with ' + strong(tag)], function () {
      return partial('tags');
    });
    return render('marks');
  } else if (url_is('/my/marks/search')) {
    const query = get_param('query');
    return query ? redirect(`/my/marks/search/${query}`) : redirect('/my/marks');
  } else if ((matches = url_match('/my/marks/search/*'))) {
    const query = set_param('query', decodeURIComponent(matches[1]));
    title(_('My Marks'), 'with search ' + strong(query));
    container.marks(() => model('marks').private_from_user_search(user, query, params));
    container.tags(() => model('tags').private_search_from_user(user, { query }));
    sidebar.register(['My', 'Tags with search ' + strong(query)], function () {
      return partial('tags');
    });
    return render('marks');
  } else if (url_is('/my/tags/autoupdate')) {
    const query = get_param('query', '');
    side_title('My', 'Tags with search ' + strong(query));
    const searchParams = { limit: get_int('limit', 50), query };
    container.tags(model('tags').private_search_from_user(user, searchParams));
    return partial('tags');
  } else if (url_is('/my/tags/autocomplete')) {
    const searchParams = { limit: get_int('limit', 10), query: get_param('search', '') };
    const tags = model('tags').private_search_from_user(user, searchParams);
    return json(tags.map(String));
  } else if (url_start_with('/my/marks')) {
    return moduleAction('mark');
  } else if (url_is('/my/')) {
    return redirect('/my/marks');
  }

  return unknown_url();
};
