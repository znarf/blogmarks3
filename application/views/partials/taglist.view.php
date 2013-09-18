<?php
helper('taglist');
$domain = domain();
$target_tag = helper('target')->tag();
$base_tag_path = relative_url($domain == 'my' ? '/my/marks/tag/' : '/marks/tag/');
$plus = relative_url($domain == 'my' ? '/img/myplus.gif' : '/img/plus.gif');

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
  <span>
    <a style="font-size:<?= $tag->_size ?>%" class="<?= isset($tag->_classname) ? $tag->_classname : 'tag' ?>" href="<?=
    $base_tag_path . urlencode($tag->label) ?>"><?= text($tag->label) ?></a>
  <?php if ($target_tag) : ?>
  <a href="<?=
    $base_tag_path . $target_tag->label . ',' . $tag->label ?>"><img src="<?= $plus ?>" width="9" height="9" alt="+" /></a>
  <?php endif ?>
  </span>
  &nbsp;
<?php endforeach ?>
