module.exports = function (params = {}) {
  const referer = params.referer;
  const token = params.token;
  const arg = replaceable('arg');
  const modalHeader = is_modal()
    ? `<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">${_('Delete Mark')}</h3>
      </div>`
    : '';
  const refererField = referer
    ? `<input type="hidden" name="referer" value="${arg(referer)}">`
    : '';

  return `<div id="content" class="fullwidth">
  <div id="content-inner">

    <div id="myModal" class="bm-delete-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      ${modalHeader}
      <div class="modal-body">
        <p>${_('Do you really want to delete this mark?')}</p>
      </div>
      <div class="modal-footer">
        <form method="post" action="${current_url()}">
          <fieldset>
            ${refererField}
            <input type="hidden" name="token" value="${token}">
            <button class="btn btn-danger confirm" name="delete" value="1" type="confirm">${_('Delete')}</button>
            <button class="btn cancel" data-dismiss="modal" aria-hidden="true">${_('Cancel')}</button>
          </fieldset>
        </form>
      </div>
    </div>

  </div>
</div> <!-- /#content -->`;
};
