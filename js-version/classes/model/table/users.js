const table = require('../table');

class users extends table {
  constructor() {
    super();
    this.classname = '\\blogmarks\\model\\resource\\user';
    this.tablename = 'bm_users';
    this.unique_indexes = ['id', 'login'];
  }

  create(set) {
    if (set.pass !== undefined) {
      set.pass = password_hash(set.pass, PASSWORD_DEFAULT);
    }
    return super.create(set);
  }

  update(where, set = []) {
    if (set.pass !== undefined) {
      set.pass = password_hash(set.pass, PASSWORD_DEFAULT);
    }
    return super.update(where, set);
  }

  validate_field(key, value, current_user = null) {
    switch (key) {
      case 'email':
        if (filter_var(value, FILTER_VALIDATE_EMAIL) === false) {
          return _('Email is invalid');
        }
        const other_user_email = this.get_one('email', value);
        if (other_user_email) {
          if (!current_user || other_user_email.id != current_user.id) {
            return _('Email is already taken');
          }
        }
        break;
      case 'login':
        if (
          filter_var(value, FILTER_VALIDATE_REGEXP, {
            options: { regexp: /^[a-zA-Z][a-z\d_]{1,20}$/ }
          }) === false
        ) {
          return _('Username is invalid');
        }
        const other_user_login = this.get_one('login', value);
        if (other_user_login) {
          if (!current_user || other_user_login.id != current_user.id) {
            return _('Username is already taken');
          }
        }
        break;
    }
  }
}

module.exports = users;
