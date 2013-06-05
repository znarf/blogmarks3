<?php
helper('taglist');
$domain = domain();
$base_tag_path = $domain == 'my' ? '/my/marks/tag/' : '/marks/tag/';
?>

<?php foreach (taglist($tags) as $tag) : ?>
  <span style="font-size:<?= $tag->_size ?>%;"><a class="tag" href="<?=
    relative_url($base_tag_path . urlencode($tag->label)) ?>"><?= text($tag->label) ?></a></span>
<?php endforeach ?>
