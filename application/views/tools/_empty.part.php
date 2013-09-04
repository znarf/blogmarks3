<h3><?= _('Empty') ?></h3>

<?php
if (is_post()) {
  return action('empty');
}
?>

<p><?php echo _('If you press the button below, you will empty all your account. Are you sure?') ?></p>

<form method="post" action="">
  <fieldset>
    <p><input class="submit" type="submit" value="<?php echo _('Empty') ?>"></p>
  </fieldset>
</form>
