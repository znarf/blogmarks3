<?php
$brand = brand();
$section = section();
$relative_url = replaceable('relative_url');
$authenticated_user = authenticated_user();
?>
<div class="navbar navbar-inverse">
  <div class="navbar-inner">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="<?= $relative_url('/marks') ?>"><?= $brand ?></a>
      <div class="nav-collapse collapse">
        <p class="navbar-text pull-right">
          <?php if ($authenticated_user) : ?>
          <?= _("Connected as") ?>
          <a href="<?= $relative_url('/my/profile/general,edit') ?>" class="navbar-link"><?= $authenticated_user->name ?></a>
          /
          <a class="navbar-link" href="<?= $relative_url('/auth/signout') ?>"><?= _("Sign Out") ?></a>
          <?php else : ?>
            <?php if (flag('enable_oauth')) : ?>
              <a class="navbar-link" href="<?= $relative_url('/oauth/connect') ?>"><?= _("Sign In with Open Collective") ?></a>
            <?php else : ?>
              <?php if (flag('enable_signup')) : ?>
                <a class="navbar-link" href="<?= $relative_url('/auth/signup') ?>"><?= _("Sign Up") ?></a>
                /
                <?php endif ?>
              <a class="navbar-link" href="<?= $relative_url('/auth/signin') ?>"><?= _("Sign In") ?></a>
            <?php endif ?>
          <?php endif ?>
        </p>
        <ul class="nav">
          <li class="<?= $section == 'public' ? 'active' : '' ?>">
            <a href="<?= $relative_url('/marks') ?>"><?= _("Public Marks") ?></a>
          </li>
          <?php if (flag('enable_social_features')) : ?>
          <li class="<?= $section == 'friends' ? 'active' : '' ?>">
            <a href="<?= $relative_url('/my/friends/marks') ?>"><?= _("Friends Marks") ?></a>
          </li>
          <?php endif ?>
          <li class="<?= $section == 'my' ? 'active' : '' ?>">
            <a href="<?= $relative_url('/my/marks') ?>"><?= _("My Marks") ?></a>
          </li>
          <li class="<?= $section == 'my' ? 'active' : '' ?>">
            <a href="<?= $relative_url('/my/marks,new') ?>"><?= _("New Mark") ?></a>
          </li>
          <li class="<?= $section == 'tools' ? 'active' : '' ?>">
            <a href="<?= $relative_url('/my/tools') ?>"><?= _("Tools") ?></a>
          </li>
        </ul>
      </div>
    </div>
</div>
