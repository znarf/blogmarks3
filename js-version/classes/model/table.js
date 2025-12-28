const amateur = global.amateur || require('../../amateur/amateur');

class table extends amateur.model.table {}

table.prototype.registry = require('../magic/registry');

module.exports = table;
