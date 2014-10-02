<?php
$users = isset($users) ? $users : helper('container')->users();
$users = is_callable($users) ? $users() : $users;
?>

<div class="friends">
  <h3><?= side_title() ?></h3>
  <?php foreach ($users as $user) : ?>
  <p class="user">
    <img class="gravatar" alt="" src="<?= $user->avatar ?>">
    <a class="user-name" href="<?= $user->url ?>"><?= $user->name ?></a><br>
    <?= _("last mark:") ?> <?= $user->last_published ?> </p>
  <?php endforeach ?>
</div>