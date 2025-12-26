class Related {
  active_users() {
    const container = helper('container');
    const marks = container.marks();
    const users = {};
    for (const mark of marks.items) {
      const user = mark.user;
      if (!users[user.id]) {
        users[user.id] = user;
        users[user.id].last_published = strftime('%d %B %Y %H:00', mark.published.getTimestamp());
      }
    }
    return Object.values(users);
  }
}

module.exports = new Related();
