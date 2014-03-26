<div id="content">
  <div id="content-inner">

    <ul class="bm-menu">
      <li class="general selected">
        <a href="<?= relative_url("/my/profile,general") ?>"><span><?= _('General') ?></span></a>
      </li>
    </ul>

  </div>
</div> <!-- /#content -->

<div id="right-bar">
  <div id="right-bar-inner">

    <?php partial('notification') ?>

    <form method="post" action="" class="form-horizontal">

      <?php $fullname_error = form_error('fullname') ?>
      <div class="control-group <?php if ($fullname_error) echo 'warning' ?>">
        <label class="control-label" for="signup_fullname"><?= _('Full Name') ?></label>
        <div class="controls">
          <input type="text" id="signup_fullname" name="fullname"
            value="<?= arg($fullname) ?>"
            required placeholder="Full Name" autocorrect="off" pattern="[^<>&amp;\|]{2,128}">
          <?php if ($fullname_error) : ?>
            <span class="help-inline"><?= text($fullname_error) ?></span>
          <?php endif ?>
        </div>
      </div>

      <?php $email_error = form_error('email') ?>
      <div class="control-group <?php if ($email_error) echo 'warning' ?>">
        <label class="control-label" for="signup_email"><?= _('Email Address') ?></label>
        <div class="controls">
          <input type="email" id="signup_email" name="email"
            value="<?= arg($email) ?>"
            required placeholder="email@domain.com" autocapitalize="off" autocorrect="off">
          <?php if ($email_error) : ?>
            <span class="help-inline"><?= text($email_error) ?></span>
          <?php else : ?>
            <span class="help-inline"><?= _('A valid email address.') ?></span>
          <?php endif ?>
        </div>
      </div>

      <?php $username_error = form_error('username') ?>
      <div class="control-group <?php if ($username_error) echo 'warning' ?>">
        <label class="control-label" for="signup_username"><?= _('Username') ?></label>
        <div class="controls">
          <input type="text" id="signup_username" name="username"
            value="<?= arg($username) ?>"
            required placeholder="username" autocapitalize="off" autocorrect="off" pattern="[a-zA-Z][a-z\d_]{1,24}">
          <?php if ($username_error) : ?>
            <span class="help-inline"><?= text($username_error) ?></span>
          <?php else : ?>
            <span class="help-inline"><?= _('Up to 24 alphanumerical characters.') ?></span>
          <?php endif ?>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="profile_timezone"><?= _('Timezone') ?></label>
        <div class="controls">
          <select id="profile_timezone" name="timezone">
          <?php
          foreach (DateTimeZone::listIdentifiers(DateTimeZone::ALL) as $value) {
              $selected = $timezone == $value ? 'selected="selected" ' : '';
              echo '<option ' . $selected . 'value="' . $value . '">' . $value . '</option>' . "\n";
          }
          ?>
          </select>
        </div>
      </div>

      <div class="control-group">
        <div class="controls">
          <button type="submit" class="btn"><?= _('Update Profile') ?></button>
        </div>
      </div>

    </form>

  </div>
</div> <!-- /#right-bar -->
