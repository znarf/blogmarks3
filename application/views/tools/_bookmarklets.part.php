  <?php

  $filename = dirname(__FILE__) . "/bookmarklet.js";

  $bookmarklet = file_get_contents($filename);

  $bookmarklet = str_replace( "\n" , "" , $bookmarklet );
  $bookmarklet = str_replace( "\r" , "" , $bookmarklet );
  $bookmarklet = str_replace( "\t" , "" , $bookmarklet );
  $bookmarklet = str_replace( " = " , "=" , $bookmarklet );
  $bookmarklet = str_replace( '{BM_HOST}' , request_host(), $bookmarklet );

  ?>

  <h3><?= _('Bookmarklet') ?></h3>

  <p><?= _('Drag this link to your browser toolbar and use it as a button to add marks. It will open a popup and post directly to your blogmarks.net account.') ?></p>

  <p><?= _('Tip: you can select a piece of text before clicking the "Add to Blogmarks" button, it will appear as the description.') ?></p>

  <p style="text-align:center"><img src="/img/bookmarklets-popup.gif" width="92" height="130"  alt=""></p>

  <p><?= _('Drag the button below to your browser toolbar:') ?></p>

  <p><span class="button"><a title="drag this link to your toolbar" onclick="alert('This link purpose is to be added in your favorite toolbar, by right-clicking or drag &amp; dropping');return false;" href="javascript:<?= $bookmarklet ?>">Add to Blogmarks</a></span></p>
