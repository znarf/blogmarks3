module.exports = function (params = {}) {
  const search = partial('search');
  const marks = params.marks !== undefined ? params.marks : helper('container').marks();
  if (marks.total === 0) {
    helper('sidebar').empty();
  }
  const notification = partial('notification');
  const marksPartial = partial('marks', { marks });
  const sidebar = helper('sidebar');

  return `${search}

<div id="content" class="${sidebar.is_empty() ? 'fullwidth' : ''}">
  <div id="content-inner">

    ${notification}

    <div class="marks-list">

      ${marksPartial}

    </div>

  </div>
</div> <!-- /#content -->

<div id="right-bar">

  ${sidebar.render()}

</div> <!-- /#right-bar -->`;
};
