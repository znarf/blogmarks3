<?php
$tag = helper('target')->tag();
$tags = helper('container')->tags();
?>

<div id="tags">

  <h3><?= tags_title() ?></h3>

  <?php if (domain() == 'my' && empty($tag)) : ?>
  <form id="liveFilter" method="get" action="">
    <p><label><?= _("search:") ?>
      <input size="10" type="text" name="search" value=""></label>
    </p>
  </form>
  <?php endif ?>

  <p class="taglist">
    <?= view('partials/taglist', ['tags' => $tags]) ?>
  </p>

</div>
