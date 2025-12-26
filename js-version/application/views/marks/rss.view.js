module.exports = function () {
  const arg = replaceable('arg');
  const marks = helper('container').marks();
  const mark_partial_args = mark_partial_args();

  const itemsSeq = marks.items
    .map((mark) => `      <rdf:li resource="${arg(mark.url)}"/>`)
    .join('\n');

  const items = marks.items
    .map((mark) => {
      const markHtml = partial('mark', { mark, ...mark_partial_args });
      return `<item rdf:about="${text(mark.url)}">
  <title>${text(mark.title)}</title>
  <link>${text(mark.url)}</link>
  <description>${text(mark.content)}</description>
  <dc:date>${mark.published.format(datetime.W3C)}</dc:date>
  <dc:creator>${text(mark.author.name)}</dc:creator>
  <dc:subject>${text(mark.public_tags.join(', '))}</dc:subject>
  <content:encoded><![CDATA[${markHtml}]]></content:encoded>
</item>`;
    })
    .join('\n');

  return `<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel rdf:about="${api_url(request_url()) + '?format=rss'}">
  <title>${strip_tags(title())}</title>
  <link>${web_url(request_url())}</link>
  <description></description>
  <items>
    <rdf:Seq>
${itemsSeq}
    </rdf:Seq>
  </items>
</channel>
${items}
</rdf:RDF>`;
};
