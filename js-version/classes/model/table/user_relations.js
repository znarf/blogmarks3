const table = require('../table');

class user_relations extends table {
  constructor() {
    super();
    this.tablename = 'bm_user_relations';
  }
}

module.exports = user_relations;
