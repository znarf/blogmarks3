class Grouper {
  static today;
  static yesterday;

  marker_month(timestamp) {
    return format_date(new Date(timestamp * 1000), 'MMMM yyyy');
  }

  marker_day(timestamp) {
    const now = Math.floor(Date.now() / 1000);
    const today = Grouper.today || (Grouper.today = format_date(new Date(now * 1000), 'dd MMMM yyyy'));
    const yesterday =
      Grouper.yesterday || (Grouper.yesterday = format_date(new Date((now - 24 * 3600) * 1000), 'dd MMMM yyyy'));

    const marker = format_date(new Date(timestamp * 1000), 'dd MMMM yyyy');
    return marker === today ? _('Today') : marker === yesterday ? _('Yesterday') : marker;
  }

  marker_hour(timestamp) {
    return format_date(new Date(timestamp * 1000), 'dd MMMM yyyy HH:00');
  }

  group(marks = []) {
    const groups = {};

    if (!marks.length) {
      return groups;
    }
    const first_mark = marks[0];
    const last_mark = marks[marks.length - 1];

    const range = format_timestamp(first_mark.published) - format_timestamp(last_mark.published);

    let group_marker;
    if (range > 2 * 30 * 24 * 3600) {
      group_marker = this.marker_month.bind(this);
    } else if (range > 2 * 24 * 3600) {
      group_marker = this.marker_day.bind(this);
    } else {
      group_marker = this.marker_hour.bind(this);
    }

    for (const mark of marks) {
      const marker = group_marker(format_timestamp(mark.published));
      if (!groups[marker]) {
        groups[marker] = [];
      }
      groups[marker].push(mark);
    }

    return groups;
  }
}

module.exports = new Grouper();
