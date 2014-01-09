<div id="content">
  <div id="content-inner">

    <?php partial('menu', ['action' => 'empty']) ?>

  </div>
</div> <!-- /#content -->

<div id="right-bar">
  <div id="right-bar-inner">

    <h3><?= _('Empty') ?></h3>

    <p><?= _('If you press the button below, you will empty all your account. Are you sure?') ?></p>

    <form method="post" action="">
      <fieldset>
        <input type="hidden" name="token" value="<?= $token ?>">
        <p><input class="submit" type="submit" value="<?= _('Empty') ?>"></p>
      </fieldset>
    </form>

  </div>
</div> <!-- /#right-bar -->
