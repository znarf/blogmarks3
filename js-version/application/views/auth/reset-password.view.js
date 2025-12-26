module.exports = function (params = {}) {
  const reset_password_token = params.reset_password_token;
  const success = params.success;
  const invalid = params.invalid;

  const formHtml = `<form class="signin form-horizontal" method="post" action="">

      <div class="control-group">
        <label class="control-label" for="password_new">${_('New Password')}</label>
        <div class="controls">
          <input
            class="text"
            type="password"
            id="password_new"
            name="password_new"
            size="24"
            value=""
            autocomplete="off"
          />
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="password_new_confirm">${_('Confirm New Password')}</label>
        <div class="controls">
          <input
            class="text"
            type="password"
            id="password_new_confirm"
            name="password_new_confirm"
            size="24"
            value=""
            autocomplete="off"
          />
        </div>
      </div>

      <div class="control-group">
        <div class="controls">
          <input type="hidden" name="reset_password_token" value="${reset_password_token}">
          <button type="submit" class="btn">${_('Update Password')}</button>
        </div>
      </div>

    </form>`;

  const successHtml = `<div class="alert alert-success">
      ${_('Your password was updated, you can now sign in again.')}
    </div>`;

  const invalidHtml = `<div class="alert alert-error">
      ${_('Invalid reset password key.')}
    </div>`;

  let bodyHtml = formHtml;
  if (success) {
    bodyHtml = successHtml;
  } else if (invalid) {
    bodyHtml = invalidHtml;
  }

  return `<div id="content" class="fullwidth">
  <div id="content-inner">

    ${partial('notification')}

    ${bodyHtml}

  </div>
</div>`;
};
