<h3><?= _('Export') ?></h3>

<h4>Atom</h4>

<p>The blogmarks.net backup is a gzip compressed Atom feed with all your marks.</p>

<p>It is our official export format and guarantee no data loss.</p>

<?php $export_url = absolute_url('/my/tools,export?download=1'); ?>

<ul class="buttons"><li><span class="button">
  <a draggable="true"
  data-downloadurl="application/x-gzip:bm3-backup.atom.xml.gz:<?= arg($export_url) ?>"
  href="<?= arg($export_url) ?>"><?= _('get your Blogmarks backup') ?></a>
</span></li></ul>

<!--
downloadurl="application/x-gzip:bm3-backup.atom.xml.gz:<?= arg(absolute_url($export_url)) ?>"
ondragstart="event.dataTransfer.setData('DownloadURL','application/x-gzip:bm3-backup.atom.xml.gz:<?= arg(absolute_url($export_url)) ?>');"
-->

<!--
<form method="post" action="">
  <fieldset>
    <p>
      <input type="hidden" name="token" value="<?= generate_token('export') ?>">
      <input class="submit" type="submit" value="<?= _('get your Blogmarks backup') ?>">
    </p>
  </fieldset>
</form>
-->

<!--
<script type="text/javascript">
window.onload = function(){
  var file = document.getElementById("export-button");
  console.log(file);
  if (typeof file.dataset === "undefined") {
    fileDetail = file.getAttribute("data-downloadurl");
  } else {
    fileDetail = file.dataset.downloadurl;
  }
  file.addEventListener("dragstart", function(event) {
    console.log('started');
    console.log(event);
    console.log(file);
    console.log(fileDetail);
    console.log(file.dataset.downloadurl);
    event.dataTransfer.setData("DownloadURL", fileDetail);
  },false);
}
</script>
-->