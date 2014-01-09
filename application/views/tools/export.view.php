<div id="content">
  <div id="content-inner">

    <?php partial('menu', ['action' => 'export']) ?>

  </div>
</div> <!-- /#content -->

<div id="right-bar">
  <div id="right-bar-inner">

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

  </div>
</div> <!-- /#right-bar -->
