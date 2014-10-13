<?php
$domain = domain();
$target_tag = helper('target')->tag();
$target_user = helper('target')->user();
if ($target_user) {
  $base_tag_path = relative_url("/user/{$target_user->login}/marks/tag/");
}
else {
  $base_tag_path = relative_url($domain == 'my' ? '/my/marks/tag/' : '/marks/tag/');
}
$plus = static_url($domain == 'my' ? '/img/myplus.gif' : '/img/plus.gif');
$tags = isset($tags) ? $tags : helper('container')->tags();
$tags = is_callable($tags) ? $tags() : $tags;
?>

<div>

  <h3><?= side_title() ?></h3>

  <p class="taglist">
    <?php
    # Special classname for private tags
    if ($domain == 'my') {
      $ratios = table('tags')->private_ratios_for_user(authenticated_user());
      foreach ($tags as $tag) {
        if (isset($ratios[$tag->label])) {
          $tag->_classname = $ratios[$tag->label] == 100 ? 'tag private' : 'tag mixed';
          $tag->_url = relative_url("/my/marks/mixed-tag/" . urlencode($tag->label));
        }
      }
    }
    ?>
    <?php foreach (helper('taglist')->compute($tags) as $tag) : ?>
      <span>
        <a style="font-size:<?= $tag->_size ?>%" class="<?= isset($tag->_classname) ? $tag->_classname : 'tag' ?>" href="<?=
        arg(isset($tag->_url) ? $tag->_url : $base_tag_path . $tag->label) ?>"><?= text($tag->label) ?></a>
      <?php if ($target_tag) : ?>
      <a href="<?=
        arg($base_tag_path . $target_tag->label . ',' . $tag->label) ?>"><img src="<?= $plus ?>" width="9" height="9" alt="+" /></a>
      <?php endif ?>
      </span>
      &nbsp;
    <?php endforeach ?>
  </p>

</div>
