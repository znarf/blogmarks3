<h3><?= _('Import') ?></h3>

<?php

if (is_post()) {
  return action('import');
}

?>

<form enctype="multipart/form-data" method="post" action="">

  <p>
  <label for="import-file"><?= _('Choose a source file...') ?></label>
  <input id="import-file" type="file" name="file" size="25">
  </p>

  <p><input class="submit" type="submit" value="<?= _('Import Now !') ?>"></p>

</form>
