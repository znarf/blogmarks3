class Related {
  active_users() {
    const container = helper('container');
    const marks = container.marks();
    const users = {};
    for (const mark of marks.items) {
      const user = mark.user;
      if (!users[user.id]) {
        users[user.id] = user;
        users[user.id].last_published = format_date(new Date(mark.published.getTime()), 'dd MMMM yyyy HH:00');
      }
    }
    return Object.values(users);
  }
}

module.exports = new Related();
