const { utcToZonedTime } = require('date-fns-tz');

class Timezone {
  constructor() {
    this.popular = [
      'Europe/Paris',
      'Europe/Berlin',
      'Europe/London',
      'America/New_York',
      'America/Los_Angeles',
      'Asia/Tokyo',
      'Asia/Hong_Kong',
    ];
    this.all_timezones = [
      'UTC',
      'Europe/London',
      'Europe/Paris',
      'Europe/Berlin',
      'Europe/Madrid',
      'Europe/Rome',
      'Europe/Zurich',
      'Europe/Stockholm',
      'Europe/Athens',
      'America/New_York',
      'America/Chicago',
      'America/Denver',
      'America/Los_Angeles',
      'America/Sao_Paulo',
      'America/Mexico_City',
      'Asia/Dubai',
      'Asia/Kolkata',
      'Asia/Shanghai',
      'Asia/Hong_Kong',
      'Asia/Tokyo',
      'Asia/Seoul',
      'Australia/Sydney',
      'Pacific/Auckland',
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

  offset_for_timezone(timezone) {
    const utc = new Date();
    const zoned = utcToZonedTime(utc, timezone);
    return Math.round((zoned.getTime() - utc.getTime()) / 1000);
  }

  all() {
    const offsets = [];
    const timezones = {};
    for (const timezone of this.all_timezones) {
      const offset = this.offset_for_timezone(timezone);
      offsets.push(offset);
      timezones[timezone] = '(' + this.format_offset(offset) + ') ' + this.format_name(timezone);
    }

    const entries = Object.entries(timezones).map(([name, label], index) => ({
      name,
      label,
      offset: offsets[index],
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
