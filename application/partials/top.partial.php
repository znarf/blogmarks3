<?php
$section = section();
?>
<div class="navbar navbar-inverse">
  <div class="navbar-inner">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="<?= relative_url('/marks') ?>"><?= brand() ?></a>
      <div class="nav-collapse collapse">
        <p class="navbar-text pull-right">
          <?php if (is_authenticated()) : ?>
          <?= _("Connected as") ?>
          <a href="<?= relative_url('/my/profile/general,edit') ?>" class="navbar-link"><?= authenticated_user()->name ?></a>
          /
          <a class="navbar-link" href="<?= relative_url('/auth/signout') ?>"><?= _("Sign Out") ?></a>
          <?php else : ?>
            <a class="navbar-link" href="<?= relative_url('/auth/signup') ?>"><?= _("Sign Up") ?></a>
            /
            <a class="navbar-link" href="<?= relative_url('/auth/signin') ?>"><?= _("Sign In") ?></a>
          <?php endif ?>
        </p>
        <ul class="nav">
          <li class="<?= $section == 'public' ? 'active' : '' ?>">
            <a href="<?= relative_url('/marks') ?>"><?= _("Public Marks") ?></a>
          </li>
          <li class="<?= $section == 'friends' ? 'active' : '' ?>">
            <a href="<?= relative_url('/my/friends/marks') ?>"><?= _("Friends Marks") ?></a>
          </li>
          <li class="<?= $section == 'my' ? 'active' : '' ?>">
            <a href="<?= relative_url('/my/marks') ?>"><?= _("My Marks") ?></a>
          </li>
          <li class="<?= $section == 'tools' ? 'active' : '' ?>">
            <a href="<?= relative_url('/my/tools') ?>"><?= _("Tools") ?></a>
          </li>
        </ul>
      </div>
    </div>
</div>
