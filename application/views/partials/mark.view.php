<?php $base_tag_path = $domain == 'my' ? '/my/marks/tag' : '/marks/tag' ?>

<div id="mark<?= $mark->id ?>" class="<?= $mark->classname($user) ?>">
  <a href="<?= arg($mark->url) ?>"><img class="screenshot" src="<?= arg($mark->screenshot) ?>" alt=""></a>
  <div class="xfolkentry">
    <h4><a class="taggedlink" href="<?= arg($mark->url) ?>"><?= text($mark->title) ?></a></h4>
    <?php if ($domain != 'my' && !target('user')) : ?>
      <a class="gravatar" href="<?= arg($mark->author->url) ?>">
        <img width="20" height="20" class="gravatar" alt="" src="<?= arg($mark->author->avatar) ?>"></a>
      <a class="public" href="<?= arg($mark->author->url) ?>"><?= text($mark->author->name) ?></a>
    <?php endif ?>
    <?php if ($mark->content) : ?>
      <div class="description"><?= $mark->contentType == 'text' ? text($mark->content) : html($mark->content) ?></div>
    <?php endif ?>
    <?php if (count($mark->tags) > 0) : ?>
      <p class="tags">
        <?php foreach ($mark->tags as $_tag) : ?>
          <?php if ($_tag->isHidden == 0) : ?>
            <a rel="tag" class="tag public_tag" href="<?=
              relative_or_absolute_url($base_tag_path . '/' . urlencode($_tag->label)) ?>"><?= text($_tag->label) ?></a>
          <?php elseif (is_authenticated_user($mark->author)) : ?>
            <a rel="tag" class="tag private_tag" href="<?=
              relative_or_absolute_url($base_tag_path . '/' . urlencode($_tag->label)) ?>"><?= text($_tag->label) ?></a>
          <?php endif ?>
        <?php endforeach ?>
      </p>
    <?php endif ?>
    <?php if (is_authenticated_user($mark->author)) : ?>
    <div class="action-bar">
      <a class="first edit" title="Edit this mark" href="<?= relative_or_absolute_url("/my/marks/{$mark->id},edit") ?>">Edit</a>
      <a class="delete" title="Delete this Mark" href="<?= relative_or_absolute_url("/my/marks/{$mark->id},delete") ?>">Delete</a>
    </div>
    <?php endif ?>
  </div>
</div>