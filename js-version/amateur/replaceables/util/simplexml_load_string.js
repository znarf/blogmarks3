function parseAttrs(raw) {
  const attrs = {};
  if (!raw) {
    return attrs;
  }
  const regex = /([\w:-]+)=\"([^\"]*)\"/g;
  let match;
  while ((match = regex.exec(raw))) {
    attrs[match[1]] = match[2];
  }
  return attrs;
}

function stripCdata(value) {
  if (!value) {
    return '';
  }
  const cdata = value.match(/<!\[CDATA\[([\s\S]*?)\]\]>/i);
  if (cdata) {
    return cdata[1];
  }
  return value;
}

function makeValue(text, attrs = {}) {
  const value = stripCdata(text || '');
  return {
    ...attrs,
    value,
    toString() {
      return this.value || '';
    }
  };
}

function getTagValue(xml, tag, allowPrefix = false) {
  if (!xml) {
    return null;
  }
  const prefix = allowPrefix ? '(?:[\\w-]+:)?' : '';
  const regex = new RegExp(`<${prefix}${tag}[^>]*>([\\s\\S]*?)<\\/${prefix}${tag}>`, 'i');
  const match = xml.match(regex);
  return match ? match[1] : null;
}

function parseLinks(xml) {
  const links = [];
  const regex = /<link\b([^>]*)>/gi;
  let match;
  while ((match = regex.exec(xml))) {
    const attrs = parseAttrs(match[1].replace(/\/$/, ''));
    if (Object.keys(attrs).length) {
      links.push(attrs);
    }
  }
  return links;
}

function parseCategories(xml) {
  const categories = [];
  const regex = /<category\b([^>]*)>/gi;
  let match;
  while ((match = regex.exec(xml))) {
    const attrs = parseAttrs(match[1].replace(/\/$/, ''));
    if (Object.keys(attrs).length) {
      categories.push(attrs);
    }
  }
  return categories;
}

function buildEntry(xml) {
  const entry = {};
  entry.title = makeValue(getTagValue(xml, 'title'));
  entry.updated = makeValue(getTagValue(xml, 'updated'));
  entry.published = makeValue(getTagValue(xml, 'published'));
  const contentMatch = xml.match(/<content\b([^>]*)>([\s\S]*?)<\/content>/i);
  if (contentMatch) {
    const attrs = parseAttrs(contentMatch[1]);
    entry.content = makeValue(contentMatch[2], attrs);
    entry.content.type = attrs.type || '';
  } else {
    entry.content = null;
  }
  entry.link = parseLinks(xml);
  entry.category = parseCategories(xml);
  entry._children = {};
  const isPrivateValue = getTagValue(xml, 'isPrivate', true);
  if (isPrivateValue !== null) {
    const value = makeValue(isPrivateValue);
    entry._children['http://blogmarks.net/ns/'] = { isPrivate: value };
    entry._children['https://blogmarks.net/ns/'] = { isPrivate: value };
  }
  entry.children = (ns) => entry._children[ns] || {};
  return entry;
}

function simplexml_load_string(xml = '') {
  const entries = [];
  const regex = /<entry\b[^>]*>([\s\S]*?)<\/entry>/gi;
  let match;
  while ((match = regex.exec(xml))) {
    entries.push(buildEntry(match[0]));
  }
  return { entry: entries };
}

module.exports = simplexml_load_string;
