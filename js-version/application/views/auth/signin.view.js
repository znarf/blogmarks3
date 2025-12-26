module.exports = function (params = {}) {
  const arg = replaceable('arg');
  const token = params.token;
  const url = request_url();
  const redirectField =
    url !== '/auth/signin'
      ? `<input type="hidden" name="redirect_url" value="${arg(url)}">`
      : '';

  return `<div id="content" class="fullwidth">
  <div id="content-inner">

    ${partial('notification')}

    <form class="signin form-horizontal" method="post" action="${relative_url('/auth/signin')}">
      <div class="control-group">
        <label class="control-label" for="inputEmail">${_('Username (or Email)')}</label>
        <div class="controls">
          <input type="text" id="inputEmail" name="username" placeholder="${_(
            'Username (or Email)'
          )}" autocapitalize="off" autocorrect="off">
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
          ${redirectField}
          <input type="hidden" name="token" value="${token}">
          <button type="submit" class="btn">Sign In</button>
        </div>
      </div>
      <div class="control-group">
        <div class="controls">
          <a href="${relative_url('/auth/forgot-password')}">${_('Forgot Password?')}</a>
        </div>
      </div>
    </form>

  </div>
</div>`;
};
