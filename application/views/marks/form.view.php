<div id="content" class="fullwidth">
  <div id="content-inner">

    <?= view('partials/notification') ?>

    <form class="new-mark" method="post" action="">
      <fieldset>

        <label for="new-mark-url">URL</label>
        <input id="new-mark-url" name="url" type="url" required class="input-block-level" value="<?= arg($url) ?>">

        <label for="new-mark-title">Title</label>
        <input id="new-mark-title" name="title" type="text" required class="input-block-level" value="<?= arg($title) ?>">

        <label for="new-mark-description">Description</label>
        <textarea name="description" rows="3" class="input-block-level"><?=
          text($description) ?></textarea>

        <div id="new-mark-description"><?=
          text($description) ?></div>

        <label for="new-mark-tags">Tags</label>
        <input id="new-mark-tags" name="tags" type="text" class="input-block-level" value="<?= arg($tags) ?>">

        <label>Visibiity</label>
        <label for="new-mark-visibility-public" class="radio inline">
          <input id="new-mark-visibility-public" name="visibility" type="radio" value="0" <?=
            $visibility == 0 ? 'checked' : '' ?>> public
        </label>
        <label for="new-mark-visibility-private" class="radio inline">
          <input id="new-mark-visibility-private" name="visibility" type="radio" value="1" <?=
            $visibility == 1 ? 'checked' : '' ?>> private
        </label>

        <div class="form-actions">
          <input type="hidden" name="token" value="<?= arg($token) ?>">
          <?php if (isset($referer)) : ?>
          <input type="hidden" name="referer" value="<?= arg($referer) ?>">
          <?php endif ?>
          <button type="submit" name="save" value="1" class="btn btn-primary">Save</button>
          <button type="submit" class="btn btn-link">Cancel</button>
        </div>

      </fieldset>
    </form>

    <script type="text/javascript">
    tags = <?php
    $tags = [];
    foreach (container('tags') as $tag) $tags[] = (string)$tag;
    echo json_encode($tags);
    ?>;
    </script>

  </div>
</div> <!-- /#content -->
