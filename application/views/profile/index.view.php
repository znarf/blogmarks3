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

      <?php $fullname_error = form_error('name') ?>
      <div class="control-group <?php if ($fullname_error) echo 'warning' ?>">
        <label class="control-label" for="profile_fullname"><?= _('Full Name') ?></label>
        <div class="controls">
          <input type="text" id="profile_fullname" name="name"
            value="<?= arg($name) ?>"
            required placeholder="Full Name" autocorrect="off" pattern="[^<>&amp;\|]{2,128}">
          <?php if ($fullname_error) : ?>
            <span class="help-block"><?= text($fullname_error) ?></span>
          <?php endif ?>
        </div>
      </div>

      <?php $email_error = form_error('email') ?>
      <div class="control-group <?php if ($email_error) echo 'warning' ?>">
        <label class="control-label" for="profile_email"><?= _('Email Address') ?></label>
        <div class="controls">
          <input type="email" id="profile_email" name="email"
            value="<?= arg($email) ?>"
            required placeholder="email@domain.com" autocapitalize="off" autocorrect="off">
          <?php if ($email_error) : ?>
            <span class="help-block"><?= text($email_error) ?></span>
          <?php endif ?>
        </div>
      </div>

      <?php $username_error = form_error('login') ?>
      <div class="control-group <?php if ($username_error) echo 'warning' ?>">
        <label class="control-label" for="profile_username"><?= _('Username') ?></label>
        <div class="controls">
          <input type="text" id="profile_username" name="login"
            value="<?= arg($login) ?>"
            required placeholder="username" autocapitalize="off" autocorrect="off" pattern="[a-zA-Z][a-z\d_]{1,24}">
          <?php if ($username_error) : ?>
            <span class="help-block"><?= text($username_error) ?></span>
          <?php endif ?>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="profile_lang"><?= _('Language') ?></label>
        <div class="controls">
          <select id="profile_lang" name="lang">
            <option value="auto">Auto</option>
            <option <?php if ($lang == 'en_US') echo 'selected' ?> value="en_US">English</option>
            <option <?php if ($lang == 'fr_FR') echo 'selected' ?> value="fr_FR">Français</option>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="profile_timezone"><?= _('Timezone') ?></label>
        <div class="controls">
          <select id="profile_timezone" name="timezone">
            <?php
            $tz = helper('timezone');
            $all_timezones = $tz->all()
            ?>
            <optgroup label="<?= _("Popular") ?>">
              <?php
              foreach ($tz->popular as $identifier) {
                $selected = $timezone == $identifier ? 'selected="selected" ' : '';
                $already_selected = empty($already_selected) ? (bool)$selected : $already_selected;
                echo '<option ' . $selected . 'value="' . $identifier . '">' . $all_timezones[$identifier] . '</option>' . "\n";
              }
              ?>
            </optgroup>
            <optgroup label="<?= _("All") ?>">
            <?php
            foreach ($all_timezones as $identifier => $label) {
                $selected = empty($already_selected) && $timezone == $identifier ? 'selected="selected" ' : '';
                echo '<option ' . $selected . 'value="' . $identifier . '">' . $label . '</option>' . "\n";
            }
            ?>
            </optgroup>
          </select>
        </div>
      </div>

      <div class="control-group">
        <div class="controls">
          <input type="hidden" name="token" value="<?= $token ?>">
          <button type="submit" class="btn"><?= _('Update Profile') ?></button>
        </div>
      </div>

    </form>

  </div>
</div> <!-- /#right-bar -->
