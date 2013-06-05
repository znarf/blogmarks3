<?php if ($message = flash_message()) : ?>
<div class="alert fade-out">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?= $message ?>
</div>
<?php endif ?>