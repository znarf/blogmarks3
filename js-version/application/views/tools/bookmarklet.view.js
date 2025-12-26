module.exports = function () {
  const filename = dirname(__FILE__) + '/bookmarklet.js';
  let bookmarklet = file_get_contents(filename);

  bookmarklet = bookmarklet.replace(/\n/g, '');
  bookmarklet = bookmarklet.replace(/\r/g, '');
  bookmarklet = bookmarklet.replace(/\t/g, '');
  bookmarklet = bookmarklet.replace(/ = /g, '=');
  bookmarklet = bookmarklet.replace('{BM_HOST}', request_host());

  return `<div id="content">
  <div id="content-inner">

    ${partial('menu', { action: 'bookmarklet' })}

  </div>
</div> <!-- /#content -->

<div id="right-bar">
  <div id="right-bar-inner">

    <h3>${_('Bookmarklet')}</h3>

    <p>${_('Drag this link to your browser toolbar and use it as a button to add marks.')}</p>

    <p>${_(
      'Tip: you can select a piece of text before clicking the "Add to Blogmarks" button, it will appear in the description.'
    )}</p>

    <p style="text-align:center"><img src="${static_url('/img/bookmarklets-popup.gif')}" width="92" height="130"  alt=""></p>

    <p>${_('Drag the button below to your browser toolbar:')}</p>

    <p><span class="button"><a title="drag this link to your toolbar" onclick="alert('This link purpose is to be added in your favorite toolbar, by right-clicking or drag &amp; dropping');return false;" href="javascript:${bookmarklet}">Add to Blogmarks</a></span></p>

  </div>
</div> <!-- /#right-bar -->`;
};
