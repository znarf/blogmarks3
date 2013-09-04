<?php
helper('taglist');
$domain = domain();
$base_tag_path = relative_url($domain == 'my' ? '/my/marks/tag/' : '/marks/tag/');

# Special classname for private tags
if ($domain == 'my') {
  $ratios = model('tags')->private_ratios_for_user(authenticated_user());
  foreach ($tags as $tag) {
    if (isset($ratios[$tag->label])) {
      $tag->_classname = $ratios[$tag->label] == 100 ? 'tag private' : 'tag mixed';
    }
  }
}
?>

<?php foreach (taglist($tags) as $tag) : ?>
  <span style="font-size:<?= $tag->_size ?>%"><a class="<?= isset($tag->_classname) ? $tag->_classname : 'tag' ?>" href="<?=
    $base_tag_path . urlencode($tag->label) ?>"><?= text($tag->label) ?></a></span>
<?php endforeach ?>
