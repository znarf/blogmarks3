module.exports = function (args = {}) {
  const action = args.action;
  const categories = {
    bookmarklet: _('Bookmarklet'),
    import: _('Import'),
    export: _('Export'),
    empty: _('Empty')
  };

  const items = Object.entries(categories)
    .map(([category, label]) => {
      const className = action === category ? `${category} selected` : category;
      return `<li class="${className}"><a href="${relative_url(
        `/my/tools,${category}`
      )}"><span>${label}</span></a></li>`;
    })
    .join('\n');

  return `<ul class="bm-menu">
${items}
</ul>`;
};
