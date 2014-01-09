<div id="content" class="fullwidth">
  <div id="content-inner">

    <?php partial('notification') ?>

    <form class="signin form-horizontal" method="post" autocomplete="off" action="<?= relative_url('/auth/signup') ?>">

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
<!--         <div class="input-prepend"> -->
    <!--       <span class="add-on">@</span> -->
          <input type="text" id="signup_username" name="username"
            value="<?= arg($username) ?>"
            required placeholder="username" autocapitalize="off" autocorrect="off" pattern="[a-zA-Z][a-z\d_]{1,24}">
<!--           </div> -->
          <?php if ($username_error) : ?>
            <span class="help-inline"><?= text($username_error) ?></span>
          <?php else : ?>
            <span class="help-inline">Up to 24 alphanumerical characters.</span>
          <?php endif ?>
        </div>
      </div>

      <?php $password_error = form_error('password') ?>
      <div class="control-group <?php if ($password_error) echo 'warning' ?>">
        <label class="control-label" for="signup_password">Password</label>
        <div class="controls">
          <input type="password" id="signup_password" name="password"
            required placeholder="" pattern="(.){6,128}">
          <?php if ($password_error) : ?>
            <span class="help-inline"><?= text($password_error) ?></span>
          <?php else : ?>
            <span class="help-inline">A minimum of 6 characters.</span>
          <?php endif ?>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="signup_password_again">Password Again</label>
        <div class="controls">
          <input type="password" id="signup_password_again" name="password_again"
            required placeholder="" pattern="(.){6,128}">
        </div>
      </div>

      <div class="control-group">
        <div class="controls">
          <?php if ('/auth/signup' != $url = request_url()) : ?>
            <input type="hidden" name="redirect_url" value="<?= $url ?>">
          <?php endif ?>
          <button type="submit" class="btn">Sign Up</button>
        </div>
      </div>

    </form>

  </div>
</div>