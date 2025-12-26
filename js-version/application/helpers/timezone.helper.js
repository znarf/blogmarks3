class Timezone {
  constructor() {
    this.popular = [
      'Europe/Paris',
      'Europe/Berlin',
      'Europe/London',
      'America/New_York',
      'America/Los_Angeles',
      'Asia/Tokyo',
      'Asia/Hong_Kong'
    ];
  }

  format_offset(offset) {
    const hours = parseInt(offset / 3600, 10);
    const minutes = Math.abs(parseInt((offset % 3600) / 60, 10));
    return 'UTC' + (offset ? sprintf('%+03d:%02d', hours, minutes) : '+00:00');
  }

  format_name(name) {
    let formatted = name.replace('/', ', ');
    formatted = formatted.replace('_', ' ');
    formatted = formatted.replace('St ', 'St. ');
    return formatted;
  }

  all() {
    const offsets = [];
    const timezones = {};

    const now = new date_time();

    for (const timezone of date_time_zone.listidentifiers()) {
      now.setTimezone(new date_time_zone(timezone));
      const offset = now.getOffset();
      offsets.push(offset);
      timezones[timezone] =
        '(' + this.format_offset(offset) + ') ' + this.format_name(timezone);
    }

    const entries = Object.entries(timezones).map(([name, label], index) => ({
      name,
      label,
      offset: offsets[index]
    }));
    entries.sort((a, b) => a.offset - b.offset);

    const sorted = {};
    for (const entry of entries) {
      sorted[entry.name] = entry.label;
    }

    return sorted;
  }
}

module.exports = new Timezone();
