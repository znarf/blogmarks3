<div id="content" class="fullwidth">
  <div id="content-inner">

    <?= view('partials/notification') ?>

    <div id="myModal" class="bm-edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form method="post" action="<?= current_url() ?>">
    <fieldset>
      <?php if (is_modal()) : ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Edit Mark</h3>
      </div>
      <?php endif ?>
      <div class="modal-body">

        <label for="new-mark-url">URL</label>
        <input id="new-mark-url" name="url" type="url" required class="input-block-level" value="<?= arg($url) ?>">

        <label for="new-mark-title">Title</label>
        <input id="new-mark-title" name="title" type="text" required class="input-block-level" value="<?= arg($title) ?>">

        <label for="new-mark-description">Description</label>
        <textarea id="new-mark-description" name="description" rows="3" class="input-block-level"><?=
          text($description) ?></textarea>

        <label for="new-mark-tags">Tags</label>
        <input id="new-mark-tags" name="tags" type="text" class="input-block-level" value="<?= arg($tags) ?>" autocapitalize="off">

        <label>Visibiity</label>
        <label for="new-mark-visibility-public" class="radio inline">
          <input id="new-mark-visibility-public" name="visibility" type="radio" value="0" <?=
            $visibility == 0 ? 'checked' : '' ?>> public
        </label>
        <label for="new-mark-visibility-private" class="radio inline">
          <input id="new-mark-visibility-private" name="visibility" type="radio" value="1" <?=
            $visibility == 1 ? 'checked' : '' ?>> private
        </label>

      </div>
      <div class="modal-footer">
        <?php if (isset($referer)) : ?>
        <input type="hidden" name="referer" value="<?= arg($referer) ?>">
        <?php endif ?>
        <input type="hidden" name="token" value="<?= $token ?>">
        <button class="btn btn-primary confirm" name="save" value="1" type="confirm">Save</button>
        <button class="btn cancel" data-dismiss="modal" aria-hidden="true">Cancel</button>
      </div>
    </fieldset>
    </form>
    </div>

    <?php /*
    <script type="text/javascript">
    tags = <?php
    $tags = [];
    foreach (container('tags') as $tag) $tags[] = (string)$tag;
    echo json_encode($tags);
    ?>;
    </script>
    */
    ?>

  </div>
</div> <!-- /#content -->
