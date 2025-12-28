function moduleAction(name, callable = null) {
  const moduleFn = global.replaceable('module');
  return moduleFn(name, callable);
}

module.exports = moduleAction;
