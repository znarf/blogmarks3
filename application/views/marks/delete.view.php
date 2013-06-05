<div id="content" class="fullwidth">
  <div id="content-inner">

    <?php /*
    <h2><span>Do you really want to delete this mark?</span></h2>

    <form class="delete-mark" method="post" action="">
      <fieldset>

        <div class="form-actions">
          <input type="hidden" name="token" value="<?= $token ?>">
          <button type="submit" name="confirm" value="1" class="btn btn-danger">Delete</button>
          <button type="submit" name="cancel" value="1" class="btn btn-link">Cancel</button>
        </div>

      </fieldset>
    </form>
    */ ?>

    <div id="myModal" class="bm-delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <?php if (is_modal()) : ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Delete Mark</h3>
      </div>
      <?php endif ?>
      <div class="modal-body">
        <p>Do you really want to delete this mark?</p>
      </div>
      <div class="modal-footer">
        <form method="post" action="<?= current_url() ?>">
          <fieldset>
            <?php if (isset($referer)) : ?>
            <input type="hidden" name="referer" value="<?= arg($referer) ?>">
            <?php endif ?>
            <input type="hidden" name="token" value="<?= $token ?>">
            <button class="btn btn-danger confirm" name="delete" value="1" type="confirm">Delete</button>
            <button class="btn cancel" data-dismiss="modal" aria-hidden="true">Cancel</button>
          </fieldset>
        </form>
      </div>
    </div>

  </div>
</div> <!-- /#content -->
