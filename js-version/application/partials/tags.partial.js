module.exports = function (args = {}) {
  const arg = replaceable('arg');
  const domainValue = domain();
  const targetTag = helper('target').tag();
  const targetUser = helper('target').user();
  const base_tag_path = targetUser
    ? relative_url(`/user/${targetUser.login}/marks/tag/`)
    : relative_url(domainValue === 'my' ? '/my/marks/tag/' : '/marks/tag/');
  const plus = static_url(domainValue === 'my' ? '/img/myplus.gif' : '/img/plus.gif');
  let tags = args.tags !== undefined ? args.tags : helper('container').tags();
  if (is_callable(tags)) {
    tags = tags();
  }

  let tagsHtml = '';
  if (tags.length > 0) {
    if (domainValue === 'my') {
      const ratios = table('tags').private_ratios_for_user(authenticated_user());
      for (const tag of tags) {
        if (ratios[tag.label] !== undefined) {
          tag._classname = ratios[tag.label] === 100 ? 'tag private' : 'tag mixed';
        }
      }
    }
    tagsHtml = helper('taglist')
      .compute(tags)
      .map((tag) => {
        const className = tag._classname ? tag._classname : 'tag';
        const href = arg(tag._url ? tag._url : base_tag_path + tag.label);
        const plusLink = targetTag
          ? `<a href="${arg(base_tag_path + targetTag.label + ',' + tag.label)}"><img src="${plus}" width="9" height="9" alt="+" /></a>`
          : '';
        return `<span>
        <a style="font-size:${tag._size}%" class="${className}" href="${href}">${text(
          tag.label
        )}</a>
      ${plusLink}
      </span>
      &nbsp;`;
      })
      .join('\n');
  } else {
    tagsHtml = '<p>No tag yet.</p>';
  }

  return `<div>

  <h3>${side_title()}</h3>

  ${tags.length > 0 ? `<p class="taglist">
    ${tagsHtml}
  </p>` : tagsHtml}

</div>`;
};
