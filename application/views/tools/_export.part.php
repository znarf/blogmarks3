<h3><?= _('Export') ?></h3>

<h4>Atom</h4>

<p>The blogmarks.net backup is a gzip compressed Atom feed with all your marks.</p>

<p>It is our official export format and guarantee no data loss.</p>

<!--
<ul class="buttons"><li><span class="button">
  <a href="/my/marks?format=atom&amp;limit=-1&amp;export=1"><?= _('get your Blogmarks backup') ?></a>
</span></li></ul>
-->

<form method="post" action="">
  <fieldset>
    <p>
      <input type="hidden" name="token" value="<?= generate_token('export') ?>">
      <input class="submit" type="submit" value="<?= _('get your Blogmarks backup') ?>">
    </p>
  </fieldset>
</form>
