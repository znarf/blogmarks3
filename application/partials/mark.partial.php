<?php

# This is a 'compiled' partial

$base_tag_prefix = $section == 'my' ? '/my/marks/tag' : ($section == 'friends' ? '/my/friends/marks/tag' : '/marks/tag' );
$base_tag_path  = relative_or_absolute_url($base_tag_prefix);
$base_mark_path = relative_or_absolute_url('/my/marks');

# First register it when it's call/included for the first time

$partial = partial('mark', function($args = []) use($base_tag_path, $base_mark_path) {

extract($args);

?>

<div id="mark<?= $mark->id ?>" class="<?= $mark->classname($user) ?>">
  <a href="<?= arg($mark->url) ?>">
    <img class="screenshot" src="<?= static_url("/img/loading.gif") ?>" data-src="<?= arg($mark->screenshot) ?>" alt="">
  </a>
  <div class="xfolkentry">
    <h4><a class="taggedlink" href="<?= arg($mark->url) ?>"><?= text($mark->title) ?></a></h4>
<?php if (in_array($section, ['public', 'friends']) && !helper('target')->user()) : ?>
      <a class="gravatar" href="<?= arg($mark->author->url) ?>">
        <img width="20" height="20" class="gravatar" alt="" src="<?= arg($mark->author->avatar) ?>"></a>
      <a class="public" href="<?= arg($mark->author->url) ?>"><?= text($mark->author->name) ?></a>
<?php endif ?>
<?php if ($mark->content) : ?>
      <div class="description"><?= $mark->contentType == 'text' ? text($mark->content) : $mark->content ?></div>
<?php endif ?>
<?php if (count($mark->tags) > 0) : ?>
      <p class="tags">
<?php foreach ($mark->tags as $_tag) : ?>
<?php if ($_tag->isHidden == 0) : ?>
        <a rel="tag" class="tag public_tag" href="<?=
          $base_tag_path . '/' . urlencode($_tag->label) ?>"><?= text($_tag->label) ?></a>
<?php elseif (is_authenticated_user($mark->author)) : ?>
        <a rel="tag" class="tag private_tag" href="<?=
          $base_tag_path . '/' . urlencode($_tag->label) ?>"><?= text($_tag->label) ?></a>
<?php endif ?>
<?php endforeach ?>
      </p>
<?php endif ?>
<?php if (is_authenticated_user($mark->author)) : ?>
    <div class="action-bar">
      <a class="first edit" title="<?= _('Edit Mark') ?>"
        href="<?= "{$base_mark_path}/{$mark->id},edit" ?>"><?= _('Edit') ?></a>
      <a class="delete" title="<?= _('Delete Mark') ?>"
        href="<?= "{$base_mark_path}/{$mark->id},delete" ?>"><?= _('Delete') ?></a>
    </div>
<?php endif ?>
  </div>
</div>

<?php

});

# And execute it now

$partial($args);
