const amateur_table = require('../../amateur/classes/model/table');

class table extends amateur_table {}

table.prototype.registry = require('../magic/registry');

module.exports = table;
