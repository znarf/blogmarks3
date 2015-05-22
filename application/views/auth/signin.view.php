<div id="content" class="fullwidth">
  <div id="content-inner">

    <?php partial('notification') ?>

    <form class="signin form-horizontal" method="post" action="<?= relative_url('/auth/signin') ?>">
      <div class="control-group">
        <label class="control-label" for="inputEmail">Username (or Email)</label>
        <div class="controls">
          <input type="text" id="inputEmail" name="username" placeholder="Username (or Email)" autocapitalize="off" autocorrect="off">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="inputPassword">Password</label>
        <div class="controls">
          <input type="password" id="inputPassword" name="password" placeholder="Password">
        </div>
      </div>
      <div class="control-group">
        <div class="controls">
          <!--
          <label class="rememberme checkbox">
            <input type="checkbox"> Remember me
          </label>
          -->
          <?php if ('/auth/signin' != $url = $_SERVER['REQUEST_URI']) : ?>
            <input type="hidden" name="redirect_url" value="<?= arg($url) ?>">
          <?php endif ?>
          <input type="hidden" name="token" value="<?= $token ?>">
          <button type="submit" class="btn">Sign In</button>
        </div>
      </div>
    </form>

  </div>
</div>