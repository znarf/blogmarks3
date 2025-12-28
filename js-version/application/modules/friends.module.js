module.exports = function () {
  const [target, container, sidebar] = helper(['target', 'container', 'sidebar']);

  domain('my');
  section('friends');
  title(_('Friends Marks'));

  if (!flag('enable_social_features')) {
    return unknown_url();
  }

  check_authenticated();
  const user = authenticated_user();

  const params = request_marks_params();
  let matches;

  if (url_is('/my/friends/marks')) {
    container.marks(() => model('marks').from_friends(user, params));
    sidebar.register(['Active', 'Friends'], function () {
      return partial('users', { users: helper('related').active_users });
    });
    return render('marks');
  } else if ((matches = url_match('/my/friends/marks/tag/*'))) {
    let tags = matches[1].split(',');
    const tag = target.tag(tags[0]);
    if (tags.length === 1) {
      title(_('Friends Marks'), _('with tag') + ' ' + strong(tag));
      container.marks(() => model('marks').from_friends_with_tag(user, tag, params));
    } else {
      tags = tags.map((slug) => table('tags').get_one('label', decodeURIComponent(slug)));
      const labels = tags.map((tagItem) => strong(tagItem));
      title(_('Friends Marks'), 'with tags ' + labels.join(' &amp; '));
      container.marks(() => model('marks').from_friends_with_tags(user, tags, params));
    }
    return render('marks');
  } else if (url_is('/my/friends/marks/search')) {
    const query = get_param('query');
    return query ? redirect(`/my/friends/marks/search/${query}`) : redirect('/my/friends/marks');
  } else if ((matches = url_match('/my/friends/marks/search/*'))) {
    const query = set_param('query', decodeURIComponent(matches[1]));
    title(_('Friends Marks'), 'with search ' + strong(query));
    container.marks(() => model('marks').from_friends_search(user, query, params));
    return render('marks');
  }

  return unknown_url();
};
