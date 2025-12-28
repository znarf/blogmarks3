const resource = require('../resource');

class mark extends resource {
  is_public() {
    return this.visibility == 0;
  }

  is_private() {
    return this.visibility == 1;
  }

  classname(user = null) {
    let classname = this.visibility == 1 ? 'mark private' : 'mark';
    if (user && user.id == this.user_id()) {
      classname += ' own';
    }
    return classname;
  }

  user_id() {
    return parseInt(this.attribute('author'), 10);
  }

  user() {
    return this.attribute('user') || this.table('users').get(this.user_id());
  }

  author() {
    return this.user;
  }

  link_id() {
    return parseInt(this.attribute('related'), 10);
  }

  related() {
    return this.table('links').get(this.link_id());
  }

  tags() {
    return this.attribute('tags') || this.table('marks_tags').from_mark(this);
  }

  public_tags() {
    return this.tags.filter(tag => !tag.isHidden);
  }

  private_tags() {
    return this.tags.filter(tag => tag.isHidden);
  }

  text() {
    if (this.contentType == 'html') {
      return new Markdownify.Converter().parseString(this.content);
    }
    return this.content;
  }

  published() {
    return new datetime(this.attribute('published'), new datetimezone('Europe/Paris'));
  }

  updated() {
    return new datetime(this.attribute('updated'), new datetimezone('Europe/Paris'));
  }

  screenshot() {
    let screenshot = this.attribute('screenshot');
    if (!screenshot) {
      screenshot = this.internal_screenshot();
      if (!screenshot) {
        screenshot = this.default_screenshot();
      }
      this.cache_attribute('screenshot', screenshot);
    }
    if (flag('rewrite_screenshot_url')) {
      screenshot = screenshot.replace('http://blogmarks.net/', absolute_url('/'));
    }
    return screenshot;
  }

  internal_screenshot() {
    const row = this.table('screenshots').for_mark(this);
    if (row) {
      return row.url;
    }
  }

  default_screenshot() {
    let url;
    try {
      url = new URL(this.url);
    } catch (error) {
      url = null;
    }
    if (url && flag('miniature_api_key')) {
      const parameters = {
        url: url.hostname,
        width: 112,
        height: 83,
        token: flag('miniature_api_key'),
      };
      return 'https://api.miniature.io/' + '?' + new URLSearchParams(parameters).toString();
    }
    const n = parseInt(String(this.attribute('id')).slice(-1), 10) + 1;
    return absolute_url(`/img/haikus/${n}.gif`);
  }

  url() {
    let url = this.attribute('url');
    if (!url) {
      url = this.related.href;
      this.cache_attribute('url', url);
    }
    return url;
  }

  cache_attribute(key, value) {
    this.attributes[key] = value;
    const cache_key = this.table('marks').cache_key('id', this.id);
    const row = cache.get(cache_key);
    if (row) {
      row[key] = value;
      cache.set(cache_key, row);
    }
  }
}

module.exports = mark;
