module.exports = function (params = {}) {
  const token = params.token;
  const results = params.results || [];

  const resultsHtml = results
    .map(([titleValue, code, message]) => {
      if (code === 200) {
        return `<li>${text(titleValue)} <span style="color:#339900">${text(message)}</span></li>`;
      }
      return `<li>${text(titleValue)} <span style="color:#FF9966">${text(message)}</span></li>`;
    })
    .join('\n');

  const formHtml = `<form enctype="multipart/form-data" method="post" action="">
      <fieldset>
        <label for="import-file">${_('Choose a source file...')}</label>
        <input id="import-file" type="file" name="file" size="25">
        <input type="hidden" name="token" value="${token}">
        <p><input class="submit" type="submit" value="${_('Import Now !')}"></p>
      </fieldset>
    </form>`;

  const contentHtml = is_post()
    ? `<ul class="importing">

    ${resultsHtml}

    </ul>`
    : formHtml;

  return `<div id="content">
  <div id="content-inner">

    ${partial('menu', { action: 'import' })}

  </div>
</div> <!-- /#content -->

<div id="right-bar">
  <div id="right-bar-inner">

    <h3>${_('Import')}</h3>

    ${contentHtml}

  </div>
</div> <!-- /#right-bar -->`;
};
