module.exports = function () {
  const message = flash_message();
  if (!message) {
    return '';
  }
  return `<div class="alert fade-out">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  ${message}
</div>`;
};
