<?php
$static_url = static_url();
?>
<!doctype html>
<html style="background:url(<?= $static_url ?>/style/img/background.png) top left repeat">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= brand() ?> | <?= strip_tags(title()) ?></title>
<link rel="icon" type="image/png" href="<?= $static_url ?>/img/favicon_<?= $domain = domain() ?>.png">
<link rel="stylesheet" type="text/css" href="<?= $static_url ?>/components/bootstrap.css/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?= $static_url ?>/components/bootstrap.css/css/bootstrap-responsive.css">
<link rel="stylesheet" type="text/css" href="<?= $static_url ?>/components/select2/select2.css">
<link rel="stylesheet" type="text/css" href="<?= $static_url ?>/style/bm.css">
<?php if (is_bookmarklet()) : ?>
<link rel="stylesheet" type="text/css" href="<?= $static_url ?>/style/bookmarklet.css">
<?php endif ?>
</head>
<body class="<?= $domain ?>">

<div class="container">

<?php partial('top') ?>

<div id="layout" class="<?= section() ?>">

<div id="title-bar">
  <h1><?= title() ?></h1>
</div> <!-- /#title-bar -->

<?= $content ?>

</div> <!-- /#layout -->

<div id="footer">
  <p>
  <span class="separator">Feedback: <a href="http://twitter.com/blogmarks">@blogmarks</a> on Twitter</span>
  <span class="separator">Designed &amp; supported by <a href="http://www.upian.com/">Upian</a></span>
  <span class="separator">Operated by <a href="http://h6e.net/">h6e</a></span>
  <span>&copy; 2004-15 the blogmarks.net team</span>
 </p>
</div> <!-- /#footer -->

</div> <!-- /#container -->

<script src="<?= $static_url ?>/components/jquery/dist/jquery.js"></script>
<script src="<?= $static_url ?>/components/bootstrap.css/js/bootstrap.js"></script>
<script src="<?= $static_url ?>/components/jquery-pjax/jquery.pjax.js"></script>
<script src="<?= $static_url ?>/components/jquery.scrollTo/jquery.scrollTo.js"></script>
<script src="<?= $static_url ?>/components/select2/select2.js"></script>
<script src="<?= $static_url ?>/js/jquery-plugins.js"></script>
<script src="<?= $static_url ?>/js/bm.js"></script>

</body>
</html>
