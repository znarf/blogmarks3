module.exports = function () {
  const arg = replaceable('arg');
  const exportValue = get_bool('export');
  const output_screenshot = !exportValue || get_bool('export_screenshot');
  const marks = helper('container').marks();
  const user = domain() === 'my' ? authenticated_user() : helper('target').user();

  const authorBlock = user
    ? `<author>
  <name>${text(user.name)}</name>
  <uri>${text(user.url)}</uri>
</author>`
    : '';

  const entries = marks.items
    .map((mark) => {
      const author = !user
        ? `<author>
    <name>${text(mark.author.name)}</name>
    <uri>${text(mark.author.url)}</uri>
  </author>`
        : '';
      const link = !exportValue ? `<link href="${text(mark.url)}"/>` : '';
      const relatedLink = `<link rel="related" href="${text(mark.url)}"/>`;

      let enclosure = '';
      if (output_screenshot && mark.screenshot) {
        const type =
          mark.screenshot.indexOf('.jpg') !== -1 ||
          mark.screenshot.indexOf('open.thumbshots.org') !== -1
            ? 'image/jpg'
            : 'image/png';
        enclosure = `<link rel="enclosure" href="${arg(mark.screenshot)}" type="${arg(type)}"/>`;
      }

      const content = mark.content
        ? `<content type="${mark.contentType}"><![CDATA[${mark.content}]]></content>`
        : '';

      const categories = mark.tags
        .map((tag) => {
          const scheme = tag.isHidden ? mark.author.url + '/private-tag' : 'https://blogmarks.net/tag/';
          const term = urlencode(tag.label);
          return `<category scheme="${arg(scheme)}" term="${arg(term)}" label="${arg(
            tag.label
          )}"/>`;
        })
        .join('\n');

      const isPrivate = mark.is_private() ? '<bm:isPrivate>1</bm:isPrivate>' : '';

      return `<entry>
  <id>tag:blogmarks.net,${mark.published.format('Y')}:${text(mark.id)}</id>
  <title>${text(mark.title)}</title>
  <updated>${mark.updated.format(datetime.RFC3339)}</updated>
  <published>${mark.published.format(datetime.RFC3339)}</published>
${author}
  ${link}
  ${relatedLink}
${enclosure}
${content}
${categories}
${isPrivate}
</entry>`;
    })
    .join('\n');

  return `<feed xmlns="http://www.w3.org/2005/Atom" xmlns:bm="https://blogmarks.net/ns/">
<id>tag:blogmarks.net,2005:marks</id>
<title>${strip_tags(title())}</title>
<updated>${date(datetime.RFC3339)}</updated>
<link rel="alternate" type="text/html" href="${web_url(request_url())}" title="${strip_tags(
    title()
  )}"/>
${authorBlock}
${entries}
</feed>`;
};
