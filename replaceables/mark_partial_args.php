<?php namespace blogmarks;

function mark_partial_args()
{
  $section = section();
  $base_tag_prefix = $section == 'my' ? '/my/marks/tag' : ($section == 'friends' ? '/my/friends/marks/tag' : '/marks/tag' );
  return [
    'base_mark_path'      => relative_or_absolute_url('/my/marks'),
    'base_tag_path'       => relative_or_absolute_url($base_tag_prefix),
    'mixed_base_tag_path' => relative_or_absolute_url('/my/marks/mixed-tag'),
    'section'             => $section,
    'target_user'         => helper('target')->user(),
    'authenticated_user'  => authenticated_user()
  ];
}
