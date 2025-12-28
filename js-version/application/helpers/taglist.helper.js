class Taglist {
  sort_prepare(str) {
    return String(str || '')
      .normalize('NFKD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^\x00-\x7F]/g, '')
      .toLowerCase();
  }

  sort_tags(tags) {
    const sorted_tags = {};
    for (const tag of tags) {
      const key = this.sort_prepare(tag.label);
      sorted_tags[key] = tag;
    }
    const keys = Object.keys(sorted_tags).sort();
    return keys.map((key) => sorted_tags[key]);
  }

  compute(tags = []) {
    if (tags.length < 1) {
      return tags;
    }
    const min_percent = 70;
    const max_percent = 130;
    let min;
    let max;
    for (const tag of tags) {
      min = min === undefined || min > tag.count ? tag.count : min;
      max = max === undefined || max < tag.count ? tag.count : max;
    }
    const diff = max !== min ? max - min : 1;
    const multiplier = (max_percent - min_percent) / diff;
    const offset = min_percent - min * multiplier;
    for (const tag of tags) {
      tag._size = Math.round(tag.count * multiplier + offset);
    }
    return this.sort_tags(tags);
  }
}

module.exports = new Taglist();
