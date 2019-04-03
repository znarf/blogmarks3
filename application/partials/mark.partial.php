<?php

return function($args) {

  extract($args);

  $arg = isset($arg) ? $arg : replaceable('arg');
  $text = isset($text) ? $text : replaceable('text');

  $is_owner = $authenticated_user && $authenticated_user->id == $mark->author->id;

  ?>
  <div id="mark<?= $mark->id ?>" class="<?= $mark->classname($authenticated_user) ?>">
    <a href="<?= $arg($mark->url) ?>">
      <img class="screenshot" src="<?= $arg($mark->screenshot) ?>" alt="">
    </a>
    <div class="xfolkentry">
      <h4><a class="taggedlink" href="<?= $arg($mark->url) ?>"><?= $text($mark->title) ?></a></h4>
  <?php if (in_array($section, ['public', 'friends']) && !$target_user) : ?>
        <a class="gravatar" href="<?= $arg($mark->author->url) ?>">
          <img width="20" height="20" class="gravatar" alt="" src="<?= $arg($mark->author->avatar) ?>"></a>
        <a class="public" href="<?= $arg($mark->author->url) ?>"><?= $text($mark->author->name) ?></a>
  <?php endif ?>
  <?php if ($mark->content) : ?>
        <div class="description"><?= $mark->contentType == 'text' ? $text($mark->content) : $mark->content ?></div>
  <?php endif ?>
  <?php if (count($mark->tags) > 0) : ?>
    <p class="tags">
<?php foreach ($mark->tags as $tag) : ?>
<?php if ($tag->isHidden == 0) : ?>
      <a rel="tag" class="tag public_tag" href="<?=
        $base_tag_path . '/' . urlencode($tag->label) ?>"><?= $text($tag->label) ?></a>
<?php elseif ($is_owner) : ?>
      <a rel="tag" class="tag private_tag" href="<?=
        $mixed_base_tag_path . '/' . urlencode($tag->label) ?>"><?= $text($tag->label) ?></a>
<?php endif ?>
<?php endforeach ?>
    </p>
  <?php endif ?>
  <?php if ($is_owner) : ?>
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

};
