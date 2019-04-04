<div id="content" class="fullwidth">
  <div id="content-inner">

    <?php partial('notification') ?>

    <?php if (empty($success)) : ?>

    <form class="signin form-horizontal" method="post" action="<?= relative_url('/auth/forgot-password') ?>">
      <div class="control-group">
        <label class="control-label" for="inputEmail"><?= _('Username (or Email)') ?></label>
        <div class="controls">
          <input type="text" id="inputEmail" name="username" placeholder="<?= _('Username (or Email)') ?>" autocapitalize="off" autocorrect="off">
        </div>
      </div>
      <div class="control-group">
        <div class="controls">
          <input type="hidden" name="token" value="<?= $token ?>">
          <button type="submit" class="btn"><?= _('Reset Password') ?></button>
        </div>
      </div>
    </form>

    <?php else : ?>

    <div class="alert alert-success">
      <?= _('An email has been sent with the instructions how to reset your password.') ?>
    </div>

    <?php endif ?>

  </div>
</div>
