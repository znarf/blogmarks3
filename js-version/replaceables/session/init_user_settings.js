function init_user_settings(user) {
  if (typeof user.timezone === 'string' && user.timezone.indexOf('/') !== -1) {
    date_default_timezone_set(user.timezone);
  }
  if (!isNaN(user.lang)) {
    if (user.lang == 1) {
      user.lang = 'fr_FR';
    }
    if (user.lang == 2) {
      user.lang = 'en_US';
    }
  }
  if (typeof user.lang === 'string' && user.lang.indexOf('_') !== -1) {
    putenv(`LC_ALL=${user.lang}.utf8`);
    setlocale(LC_ALL, `${user.lang}.utf8`);
  }
}

module.exports = init_user_settings;
