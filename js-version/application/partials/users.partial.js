module.exports = function (args = {}) {
  let users = args.users !== undefined ? args.users : helper('container').users();
  if (typeof users === 'function') {
    users = users();
  }

  const usersHtml = users
    .map(
      (user) => `<p class="user">
    <img class="gravatar" alt="" src="${user.avatar}">
    <a class="user-name" href="${user.url}">${user.name}</a><br>
    ${_('last mark:')} ${user.last_published} </p>`
    )
    .join('\n');

  return `<div class="friends">
  <h3>${side_title()}</h3>
  ${usersHtml}
</div>`;
};
