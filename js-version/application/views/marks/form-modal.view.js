module.exports = function (params = {}) {
  const arg = replaceable('arg');
  const url = params.url || '';
  const titleValue = params.title || '';
  const description = params.description || '';
  const tags = params.tags || '';
  const private_tags = params.private_tags || '';
  const visibility = params.visibility !== undefined ? params.visibility : 0;
  const referer = params.referer;
  const token = params.token;

  const modalHeader = is_modal()
    ? `<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">${_('Edit Mark')}</h3>
      </div>`
    : '';
  const refererField = referer
    ? `<input type="hidden" name="referer" value="${arg(referer)}">`
    : '';
  const bookmarkletField = is_bookmarklet()
    ? '<input type="hidden" name="bookmarklet" value="1">'
    : '';

  return `<div id="content" class="fullwidth">
  <div id="content-inner">

    ${partial('notification')}

    <div id="myModal" class="bm-edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form method="post" action="${current_url()}">
    <fieldset>
      ${modalHeader}
      <div class="modal-body">

        <label for="new-mark-url">${_('URL')}</label>
        <input id="new-mark-url" name="url" type="url" required class="input-block-level" value="${arg(
          url
        )}">

        <label for="new-mark-title">${_('Title')}</label>
        <input id="new-mark-title" name="title" type="text" required class="input-block-level" value="${arg(
          titleValue
        )}">

        <label for="new-mark-description">Description</label>
        <textarea id="new-mark-description" name="description" rows="3" class="input-block-level">${text(
          description
        )}</textarea>

        <label for="mark-form-tags">${_('Public Tags')}</label>
        <input id="mark-form-tags" name="tags" type="text" class="input-block-level" value="${arg(
          tags
        )}" autocapitalize="off">

        <label for="mark-form-private-tags">${_('Private Tags')}</label>
        <input id="mark-form-private-tags" name="private_tags" type="text" class="input-block-level" value="${arg(
          private_tags
        )}" autocapitalize="off">

        <label>${_('Visibiity')}</label>
        <label for="new-mark-visibility-public" class="radio inline">
          <input id="new-mark-visibility-public" name="visibility" type="radio" value="0" ${
            visibility == 0 ? 'checked' : ''
          }> ${_('public')}
        </label>
        <label for="new-mark-visibility-private" class="radio inline">
          <input id="new-mark-visibility-private" name="visibility" type="radio" value="1" ${
            visibility == 1 ? 'checked' : ''
          }> ${_('private')}
        </label>

      </div>
      <div class="modal-footer">
        ${refererField}
        ${bookmarkletField}
        <input type="hidden" name="token" value="${token}">
        <button class="btn btn-primary confirm" name="save" value="1" type="confirm">${_('Save')}</button>
        <button class="btn cancel" data-dismiss="modal" aria-hidden="true">${_('Cancel')}</button>
      </div>
    </fieldset>
    </form>
    </div>

  </div>
</div> <!-- /#content -->`;
};
