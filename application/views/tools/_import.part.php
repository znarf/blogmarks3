<h3><?= _('Import') ?></h3>

<?php if (is_post()) : ?>

<ul class="importing">

<?php foreach ($results as $result) : list($title, $code, $message) = $result; ?>
<?php if ($code == 200) : ?>
  <li><?= text($title) ?> <span style="color:#339900"><?= text($message) ?></span></li>
<?php else : ?>
  <li><?= text($title) ?> <span style="color:#FF9966"><?= text($message) ?></span></li>
<?php endif ?>
<?php endforeach ?>

</ul>

<?php else : ?>

<form enctype="multipart/form-data" method="post" action="">
  <fieldset>
    <label for="import-file"><?= _('Choose a source file...') ?></label>
    <input id="import-file" type="file" name="file" size="25">
    <input type="hidden" name="token" value="<?= $token ?>">
    <p><input class="submit" type="submit" value="<?= _('Import Now !') ?>"></p>
  </fieldset>
</form>

<?php endif ?>
