function mark_partial_args() {
  const section = blogmarks.section();
  const base_tag_prefix =
    section === 'my' ? '/my/marks/tag' : section === 'friends' ? '/my/friends/marks/tag' : '/marks/tag';
  return {
    base_mark_path: blogmarks.relative_or_absolute_url('/my/marks'),
    base_tag_path: blogmarks.relative_or_absolute_url(base_tag_prefix),
    section,
    target_user: helper('target').user(),
    authenticated_user: blogmarks.authenticated_user()
  };
}

module.exports = mark_partial_args;
