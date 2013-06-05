<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?= brand() ?> | <?= strip_tags(title()) ?></title>
<link rel="stylesheet" type="text/css" media="screen" href="<?= static_url() ?>/components/bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?= static_url() ?>/components/select2/select2.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?= static_url() ?>/style/bm.css.php">
</head>
<body class="<?= domain() ?> bookmarklet">

<div class="container">

<div id="layout">

<div id="title-bar">
  <h1><?= title() ?></h1>
</div> <!-- /#title-bar -->

<?= $content ?>

</div> <!-- /#layout -->

</div> <!-- /#container -->

<script src="<?= static_url() ?>/components/jquery/jquery.js"></script>
<script src="<?= static_url() ?>/components/jquery-pjax/jquery.pjax.js"></script>
<script src="<?= static_url() ?>/components/jquery-scrollto/jquery.scrollTo.js"></script>
<script src="<?= static_url() ?>/components/select2/select2.js"></script>
<script src="<?= static_url() ?>/js/jquery-plugins.js"></script>
<script src="<?= static_url() ?>/js/bm.js"></script>

</body>
</html>