function mark_partial_args() {
  const sectionValue = section();
  const base_tag_prefix =
    sectionValue === 'my'
      ? '/my/marks/tag'
      : sectionValue === 'friends'
        ? '/my/friends/marks/tag'
        : '/marks/tag';
  return {
    base_mark_path: relative_or_absolute_url('/my/marks'),
    base_tag_path: relative_or_absolute_url(base_tag_prefix),
    section: sectionValue,
    target_user: helper('target').user(),
    authenticated_user: authenticated_user(),
  };
}

module.exports = mark_partial_args;
