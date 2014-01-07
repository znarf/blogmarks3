<!doctype html>
<html style="background:url(<?= static_url() ?>/style/img/background.png) top left repeat">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= brand() ?> | <?= strip_tags(title()) ?></title>
<link rel="icon" type="image/png" href="<?= $static_url = static_url() ?>/img/favicon_<?= domain() ?>.png">
<link rel="stylesheet" type="text/css" href="<?= static_url() ?>/components/bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?= $static_url ?>/components/bootstrap/css/bootstrap-responsive.css">
<link rel="stylesheet" type="text/css" href="<?= $static_url ?>/components/select2/select2.css">
<link rel="stylesheet" type="text/css" href="<?= $static_url ?>/style/bm.css.php">
</head>
<body class="<?= domain() ?>">

<div class="container">

<?= view('partials/top') ?>

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
  <span>&copy; 2004-14 the blogmarks.net team</span>
 </p>
</div> <!-- /#footer -->

</div> <!-- /#container -->

<script src="<?= $static_url ?>/components/jquery/jquery.js"></script>
<script src="<?= $static_url ?>/components/bootstrap/js/bootstrap.js"></script>
<script src="<?= $static_url ?>/components/jquery-pjax/jquery.pjax.js"></script>
<script src="<?= $static_url ?>/components/jquery-scrollto/jquery.scrollTo.js"></script>
<script src="<?= $static_url ?>/components/select2/select2.js"></script>
<script src="<?= $static_url ?>/components/unveil/jquery.unveil.js"></script>
<script src="<?= $static_url ?>/js/jquery-plugins.js"></script>
<script src="<?= $static_url ?>/js/bm.js"></script>

</body>
</html>