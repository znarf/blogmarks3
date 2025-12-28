class date_time {
  constructor() {
    this.date = new Date();
    this.timezone = null;
  }

  setTimezone(tz) {
    this.timezone = tz;
  }

  getOffset() {
    if (this.timezone && typeof this.timezone.offset === 'number') {
      return this.timezone.offset;
    }
    return -this.date.getTimezoneOffset() * 60;
  }
}

class date_time_zone {
  constructor(name) {
    this.name = name;
    this.offset = 0;
  }

  static listidentifiers() {
    return [];
  }
}

global.date_time = date_time;
global.date_time_zone = date_time_zone;

module.exports = {
  date_time,
  date_time_zone
};
