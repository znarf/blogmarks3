<div id="content">
  <div id="content-inner">

    <ul class="bm-menu">
    <?php

    $categories = [
      'general' => _('General'),
    ];

    foreach ($categories as $category => $label) {
        $class = $action == $category ? "$category selected" : $category;
        echo
        '<li class="' . $class . '"><a href="' . relative_url("/my/profile,{$category}") . '">' .
          '<span>' . $label . '</span>' .
        '</a></li>' . "\n";
    }

    ?>
    </ul>

  </div>
</div> <!-- /#content -->

<div id="right-bar">
  <div id="right-bar-inner">

    <?php partial('notification') ?>

    <form method="post" action="" class="form-horizontal">

      <?php $fullname_error = form_error('fullname') ?>
      <div class="control-group <?php if ($fullname_error) echo 'warning' ?>">
        <label class="control-label" for="signup_fullname">Full Name</label>
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
        <label class="control-label" for="signup_email">Email Address</label>
        <div class="controls">
          <input type="email" id="signup_email" name="email"
            value="<?= arg($email) ?>"
            required placeholder="email@domain.com" autocapitalize="off" autocorrect="off">
          <?php if ($email_error) : ?>
            <span class="help-inline"><?= text($email_error) ?></span>
          <?php else : ?>
            <span class="help-inline">A valid email address.</span>
          <?php endif ?>
        </div>
      </div>

      <?php $username_error = form_error('username') ?>
      <div class="control-group <?php if ($username_error) echo 'warning' ?>">
        <label class="control-label" for="signup_username">Username</label>
        <div class="controls">
          <input type="text" id="signup_username" name="username"
            value="<?= arg($username) ?>"
            required placeholder="username" autocapitalize="off" autocorrect="off" pattern="[a-zA-Z][a-z\d_]{1,24}">
          <?php if ($username_error) : ?>
            <span class="help-inline"><?= text($username_error) ?></span>
          <?php else : ?>
            <span class="help-inline">Up to 24 alphanumerical characters.</span>
          <?php endif ?>
        </div>
      </div>

      <div class="control-group">
        <div class="controls">
          <button type="submit" class="btn">Update Profile</button>
        </div>
      </div>

    </form>

  </div>
</div> <!-- /#right-bar -->
