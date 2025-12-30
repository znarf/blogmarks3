function moduleAction(name, callable = null) {
  const moduleFn = replaceable('module');
  return moduleFn(name, callable);
}

module.exports = moduleAction;
