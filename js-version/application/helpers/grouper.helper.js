class Grouper {
  static today;
  static yesterday;

  marker_month(timestamp) {
    return strftime('%B %Y', timestamp);
  }

  marker_day(timestamp) {
    const format = '%d %B %Y';

    const today = Grouper.today || (Grouper.today = strftime(format));
    const yesterday =
      Grouper.yesterday || (Grouper.yesterday = strftime(format, time() - 24 * 3600));

    const marker = strftime(format, timestamp);
    return marker === today ? _('Today') : marker === yesterday ? _('Yesterday') : marker;
  }

  marker_hour(timestamp) {
    return strftime('%d %B %Y %H:00', timestamp);
  }

  group(marks = []) {
    const groups = {};

    const first_mark = marks[0];
    const last_mark = marks[marks.length - 1];

    const range = first_mark.published.getTimestamp() - last_mark.published.getTimestamp();

    let group_marker;
    if (range > 2 * 30 * 24 * 3600) {
      group_marker = this.marker_month.bind(this);
    } else if (range > 2 * 24 * 3600) {
      group_marker = this.marker_day.bind(this);
    } else {
      group_marker = this.marker_hour.bind(this);
    }

    for (const mark of marks) {
      const marker = group_marker(mark.published.getTimestamp());
      if (!groups[marker]) {
        groups[marker] = [];
      }
      groups[marker].push(mark);
    }

    return groups;
  }
}

module.exports = new Grouper();
