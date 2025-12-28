const resource = require('../resource');

class user extends resource {
  constructor() {
    super();
    this.default_avatar = '/img/default-gravatar.gif';
  }

  username() {
    return this.attribute('login');
  }

  name() {
    const name = this.attribute('name');
    return name ? name : this.attribute('login');
  }

  url() {
    return web_url(`/user/${this.attribute('login')}`);
  }

  avatar(size = 80) {
    const avatar = this.attribute('avatar');
    if (avatar && avatar.indexOf('@') !== -1) {
      return this.gravatar(avatar, size);
    }
    const email = this.attribute('email');
    if (email && email.indexOf('@') !== -1) {
      return this.gravatar(email, size);
    }
    return this.default_avatar;
  }

  gravatar(email, size = 80) {
    return (
      'https://www.gravatar.com/avatar.php?gravatar_id=' +
      md5(email) +
      '&size=' +
      size +
      '&d=' +
      urlencode(this.default_avatar)
    );
  }

  mark_with_link(link) {
    return this.table('marks').fetch_object({ related: link.id, author: this.id });
  }

  verify_passsword(password) {
    if (/^[a-f0-9]{32}$/.test(this.pass)) {
      if (this.pass === md5(password)) {
        if (flag('db_migrate_password')) {
          this.table('users').update(this, { pass: password });
        }
        return true;
      }
      return false;
    }
    return password_verify(password, this.pass);
  }

  generate_activation_key() {
    const activation_key = amateur.generate_phrase();
    this.table('users').update(this, { activationkey: activation_key });
    return activation_key;
  }

  following_ids() {
    return this.table('user_relations').where({ user: this.id }).fetch_ids('contact');
  }

  follower_ids() {
    return this.table('user_relations').where({ contact: this.id }).fetch_ids('user');
  }

  toString() {
    return this.name();
  }
}

module.exports = user;
