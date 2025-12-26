module.exports = function (content = '') {
  return `<div id="title-bar">
  <h1>${title()}</h1>
  ${partial('search')}
</div> <!-- /#title-bar -->

${content}`;
};
