module.exports = function (params = {}) {
  const arg = replaceable('arg');
  const fullname_error = form_error('name');
  const email_error = form_error('email');
  const username_error = form_error('login');

  const tz = helper('timezone');
  const all_timezones = tz.all();
  let already_selected = false;

  const popularOptions = tz.popular
    .map((identifier) => {
      const selected = params.timezone === identifier ? 'selected="selected" ' : '';
      if (!already_selected && selected) {
        already_selected = true;
      }
      return `<option ${selected}value="${identifier}">${all_timezones[identifier]}</option>`;
    })
    .join('\n');

  const allOptions = Object.entries(all_timezones)
    .map(([identifier, label]) => {
      const selected = !already_selected && params.timezone === identifier ? 'selected="selected" ' : '';
      return `<option ${selected}value="${identifier}">${label}</option>`;
    })
    .join('\n');

  return `<div id="content">
  <div id="content-inner">

    <ul class="bm-menu">
      <li class="general selected">
        <a href="${relative_url('/my/profile,general')}"><span>${_('General')}</span></a>
      </li>
    </ul>

  </div>
</div> <!-- /#content -->

<div id="right-bar">
  <div id="right-bar-inner">

    ${partial('notification')}

    <h3>${_('General')}</h3>

    <form method="post" action="" class="form-horizontal">

      <div class="control-group ${fullname_error ? 'warning' : ''}">
        <label class="control-label" for="profile_fullname">${_('Full Name')}</label>
        <div class="controls">
          <input type="text" id="profile_fullname" name="name"
            value="${arg(params.name)}"
            required placeholder="Full Name" autocorrect="off" pattern="[^<>&amp;|]{2,128}">
          ${
            fullname_error
              ? `<span class="help-block">${text(fullname_error)}</span>`
              : ''
          }
        </div>
      </div>

      <div class="control-group ${email_error ? 'warning' : ''}">
        <label class="control-label" for="profile_email">${_('Email Address')}</label>
        <div class="controls">
          <input type="email" id="profile_email" name="email"
            value="${arg(params.email)}"
            required placeholder="email@domain.com" autocapitalize="off" autocorrect="off">
          ${
            email_error
              ? `<span class="help-block">${text(email_error)}</span>`
              : ''
          }
        </div>
      </div>

      <div class="control-group ${username_error ? 'warning' : ''}">
        <label class="control-label" for="profile_username">${_('Username')}</label>
        <div class="controls">
          <input type="text" id="profile_username" name="login"
            value="${arg(params.login)}"
            required placeholder="username" autocapitalize="off" autocorrect="off" pattern="[a-zA-Z][a-z0-9_]{1,24}">
          ${
            username_error
              ? `<span class="help-block">${text(username_error)}</span>`
              : ''
          }
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="profile_lang">${_('Language')}</label>
        <div class="controls">
          <select id="profile_lang" name="lang">
            <option value="auto">Auto</option>
            <option ${params.lang === 'en_US' ? 'selected' : ''} value="en_US">English</option>
            <option ${params.lang === 'fr_FR' ? 'selected' : ''} value="fr_FR">Français</option>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label" for="profile_timezone">${_('Timezone')}</label>
        <div class="controls">
          <select id="profile_timezone" name="timezone">
            <optgroup label="${_('Popular')}">
              ${popularOptions}
            </optgroup>
            <optgroup label="${_('All')}">
            ${allOptions}
            </optgroup>
          </select>
        </div>
      </div>

      <div class="control-group">
        <div class="controls">
          <input type="hidden" name="token" value="${params.update_profile_token}">
          <button type="submit" class="btn">${_('Update Profile')}</button>
        </div>
      </div>

    </form>

    <h3>${_('Update Password')}</h3>

    <form method="post" action="${relative_url('/my/profile,password')}" class="form-horizontal">

      <div class="control-group">
        <label class="control-label" for="password_current">${_('Currrent Password')}</label>
        <div class="controls">
          <input
            class="text"
            type="password"
            id="password_current"
            name="password_current"
            size="24"
            value=""
            autocomplete="off"
          />
        </div>
      </div>

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
          <input type="hidden" name="token" value="${params.update_password_token}">
          <button type="submit" class="btn">${_('Update Password')}</button>
        </div>
      </div>

    </form>

  </div>
</div> <!-- /#right-bar -->`;
};
