module.exports = function (args = {}) {
  const mark = args.mark;
  const arg = args.arg || replaceable('arg');
  const text = args.text || replaceable('text');
  const sectionName = args.section;
  const target_user = args.target_user;
  const authenticated_user = args.authenticated_user;
  const base_tag_path = args.base_tag_path;
  const base_mark_path = args.base_mark_path;

  const is_owner = authenticated_user && authenticated_user.id === mark.author.id;

  const authorHtml =
    ['public', 'friends'].includes(sectionName) && !target_user
      ? `<a class="gravatar" href="${arg(mark.author.url)}">
          <img width="20" height="20" class="gravatar" alt="" src="${arg(
            mark.author.avatar
          )}"></a>
        <a class="public" href="${arg(mark.author.url)}">${text(
          mark.author.name
        )}</a>`
      : '';

  const descriptionHtml = mark.content
    ? `<div class="description">${
        mark.contentType === 'text' ? text(mark.content) : mark.content
      }</div>`
    : '';

  let tagsHtml = '';
  if (mark.tags && mark.tags.length > 0) {
    const items = mark.tags
      .map((tag) => {
        if (tag.isHidden === 0) {
          return `<a rel="tag" class="tag public_tag" href="${
            base_tag_path + '/' + urlencode(tag.label)
          }">${text(tag.label)}</a>`;
        }
        if (is_owner) {
          return `<a rel="tag" class="tag private_tag" href="${
            base_tag_path + '/' + urlencode(tag.label)
          }">${text(tag.label)}</a>`;
        }
        return '';
      })
      .join('\n');
    tagsHtml = `<p class="tags">
${items}
    </p>`;
  }

  const actionHtml = is_owner
    ? `<div class="action-bar">
        <a class="first edit" title="${_('Edit Mark')}" href="${
          base_mark_path + '/' + mark.id + ',edit'
        }">${_('Edit')}</a>
        <a class="delete" title="${_('Delete Mark')}" href="${
          base_mark_path + '/' + mark.id + ',delete'
        }">${_('Delete')}</a>
      </div>`
    : '';

  return `<div id="mark${mark.id}" class="${mark.classname(authenticated_user)}">
    <a href="${arg(mark.url)}">
      <img class="screenshot" src="${arg(mark.screenshot)}" alt="">
    </a>
    <div class="xfolkentry">
      <h4><a class="taggedlink" href="${arg(mark.url)}">${text(
        mark.title
      )}</a></h4>
  ${authorHtml}
  ${descriptionHtml}
  ${tagsHtml}
  ${actionHtml}
    </div>
  </div>`;
};
