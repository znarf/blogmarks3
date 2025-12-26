module.exports = function () {
  const brandLabel = brand();
  const sectionName = section();
  const relativeUrl = replaceable('relative_url');
  const authenticatedUser = authenticated_user();

  const authLinks = authenticatedUser
    ? `${_('Connected as')}
          <a href="${relativeUrl('/my/profile/general,edit')}" class="navbar-link">${
            authenticatedUser.name
          }</a>
          /
          <a class="navbar-link" href="${relativeUrl('/auth/signout')}">${_('Sign Out')}</a>`
    : `${
        flag('enable_signup')
          ? `<a class="navbar-link" href="${relativeUrl('/auth/signup')}">${_('Sign Up')}</a>
            /`
          : ''
      }
            <a class="navbar-link" href="${relativeUrl('/auth/signin')}">${_('Sign In')}</a>`;

  const friendsLink = flag('enable_social_features')
    ? `<li class="${sectionName === 'friends' ? 'active' : ''}">
            <a href="${relativeUrl('/my/friends/marks')}">${_('Friends Marks')}</a>
          </li>`
    : '';

  return `<div class="navbar navbar-inverse">
  <div class="navbar-inner">
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <a class="brand" href="${relativeUrl('/marks')}">${brandLabel}</a>
      <div class="nav-collapse collapse">
        <p class="navbar-text pull-right">
          ${authLinks}
        </p>
        <ul class="nav">
          <li class="${sectionName === 'public' ? 'active' : ''}">
            <a href="${relativeUrl('/marks')}">${_('Public Marks')}</a>
          </li>
          ${friendsLink}
          <li class="${sectionName === 'my' ? 'active' : ''}">
            <a href="${relativeUrl('/my/marks')}">${_('My Marks')}</a>
          </li>
          <li class="${sectionName === 'my' ? 'active' : ''}">
            <a href="${relativeUrl('/my/marks,new')}">${_('New Mark')}</a>
          </li>
          <li class="${sectionName === 'tools' ? 'active' : ''}">
            <a href="${relativeUrl('/my/tools')}">${_('Tools')}</a>
          </li>
        </ul>
      </div>
    </div>
</div>`;
};
