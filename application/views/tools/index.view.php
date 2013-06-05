<div id="content">
  <div id="content-inner">

    <?php include '_menu.part.php' ?>

  </div>
</div> <!-- /#content -->

<div id="right-bar">
  <div id="right-bar-inner">

    <?php
    switch ($action) {
      case 'bookmarklets': include '_bookmarklets.part.php'; break;
      case 'import': include '_import.part.php'; break;
      case 'empty': include '_empty.part.php'; break;
    }
    ?>

  </div>
</div> <!-- /#right-bar -->
