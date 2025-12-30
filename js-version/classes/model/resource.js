const amateur_resource = require('../../amateur/classes/model/resource');

class resource extends amateur_resource {}

resource.prototype.registry = require('../magic/registry');

module.exports = resource;
