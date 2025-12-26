module.exports = function (params = {}) {
  const arg = replaceable('arg');
  const fullname_error = form_error('fullname');
  const email_error = form_error('email');
  const username_error = form_error('username');
  const password_error = form_error('password');
  const url = request_url();
  const redirectField =
    url !== '/auth/signup'
      ? `<input type="hidden" name="redirect_url" value="${url}">`
      : '';

  return `<div id="content" class="fullwidth">
  <div id="content-inner">

    ${partial('notification')}

    <form class="signin form-horizontal" method="post" autocomplete="off" action="${relative_url(
      '/auth/signup'
    )}">

      <div class="control-group ${fullname_error ? 'warning' : ''}">
        <label class="control-label" for="signup_fullname">Full Name</label>
        <div class="controls">
          <input type="text" id="signup_fullname" name="fullname"
            value="${arg(params.fullname)}"
            required placeholder="Full Name" autocorrect="off" pattern="[^<>&amp;\|]{2,128}">
          ${
            fullname_error
              ? `<span class="help-inline">${text(fullname_error)}</span>`
              : ''
          }
        </div>
      </div>

      <div class="control-group ${email_error ? 'warning' : ''}">
        <label class="control-label" for="signup_email">Email Address</label>
        <div class="controls">
          <input type="email" id="signup_email" name="email"
            value="${arg(params.email)}"
            required placeholder="email@domain.com" autocapitalize="off" autocorrect="off">
          ${
            email_error
              ? `<span class="help-inline">${text(email_error)}</span>`
              : '<span class="help-inline">A valid email address.</span>'
          }
        </div>
      </div>

      <div class="control-group ${username_error ? 'warning' : ''}">
        <label class="control-label" for="signup_username">Username</label>
        <div class="controls">
          <input type="text" id="signup_username" name="username"
            value="${arg(params.username)}"
            required placeholder="username" autocapitalize="off" autocorrect="off" pattern="[a-zA-Z][a-z\d_]{1,24}">
          ${
            username_error
              ? `<span class="help-inline">${text(username_error)}</span>`
              : '<span class="help-inline">Up to 24 alphanumerical characters.</span>'
          }
        </div>
      </div>

      <div class="control-group ${password_error ? 'warning' : ''}">
        <label class="control-label" for="signup_password">Password</label>
        <div class="controls">
          <input type="password" id="signup_password" name="password"
            required placeholder="" pattern="(.){6,128}">
          ${
            password_error
              ? `<span class="help-inline">${text(password_error)}</span>`
              : '<span class="help-inline">A minimum of 6 characters.</span>'
          }
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
          ${redirectField}
          <button type="submit" class="btn">Sign Up</button>
        </div>
      </div>

    </form>

  </div>
</div>`;
};
