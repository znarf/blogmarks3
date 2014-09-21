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
            <span class="help-block"><?= text($fullname_error) ?></span>
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
            <span class="help-block"><?= text($email_error) ?></span>
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
            <span class="help-block"><?= text($username_error) ?></span>
          <?php endif ?>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="profile_lang"><?= _('Language') ?></label>
        <div class="controls">
          <select id="profile_language" name="language">
            <option value="0">Auto</option>
            <option value="1">English</option>
            <option value="2">Français</option>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="profile_timezone"><?= _('Timezone') ?></label>
        <div class="controls">
          <select id="profile_timezone" name="timezone">
            <?php
            $timezone = helper('timezone');
            $list = $timezone->list();
            ?>
            <optgroup label="<?= _("Popular") ?>">
              <?php foreach ($timezone->popular as $identifier) : ?>
              <option value="<?= $identifier ?>"><?= $list[$identifier] ?></option>
              <?php endforeach ?>
            </optgroup>
            <optgroup label="<?= _("All") ?>">
            <?php
            foreach ($list as $identifier => $label) {
                $selected = $timezone == $identifier ? 'selected="selected" ' : '';
                echo '<option ' . $selected . 'value="' . $identifier . '">' . $label . '</option>' . "\n";
            }
            ?>
            </optgroup>
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
