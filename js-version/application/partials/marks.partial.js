module.exports = function (args = {}) {
  const marks = args.marks !== undefined ? args.marks : helper('container').marks();
  const markPartialArgs = mark_partial_args();

  if (marks.total > 0) {
    const groups = helper('grouper').group(marks.items);

    const groupsHtml = Object.entries(groups)
      .map(([group, items]) => {
        const itemsHtml = items
          .map((mark) => partial('mark', { mark, ...markPartialArgs }))
          .join('\n');
        return `<h2><span>${group}</span></h2>

${itemsHtml}`;
      })
      .join('\n');

    let pagination = '';
    if (marks.next) {
      const more =
        marks.params.order === 'asc'
          ? { order: 'asc', after: marks.next }
          : { order: 'desc', before: marks.next };
      pagination = `<div id="pagination">
  <a rel="next" class="page more" href="?${http_build_query(more)}">more</a>
</div> <!-- /#pagination -->`;
    } else {
      pagination = '<h2 style="margin-bottom:1em"><span>The End</span></h2>';
    }

    return `${groupsHtml}

${pagination}

<div id="feed">
  <a href="${absolute_url(request_url() + '?format=atom')}">
    <img width="32" height="32" src="${static_url('/img/feed-icon.svg')}">
  </a>
</div> <!-- /#feed -->`;
  }

  const isMy = domain() === 'my';
  const targetTag = helper('target').tag();
  const targetUser = helper('target').user();
  const emptyMessage = isMy
    ? '<p>To easily add new marks, install the <a href="/my/tools,bookmarklet">the bookmarklet</a>.</p>\n    <p>You can also use <a href="/my/marks,new">the form</a> directly.</a>.</p>'
    : !targetTag && !targetUser
      ? '<p>This Blogmarks instance looks empty.</p>'
      : '';

  return `<div style="margin:3em 5em">
    <p>No mark to see here yet.</p>
    ${emptyMessage}
  </div>

<div id="feed">
  <a href="${absolute_url(request_url() + '?format=atom')}">
    <img width="32" height="32" src="${static_url('/img/feed-icon.svg')}">
  </a>
</div> <!-- /#feed -->`;
};
