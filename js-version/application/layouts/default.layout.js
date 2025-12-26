module.exports = function (content = '') {
  const staticUrl = static_url();
  const domainValue = domain();
  const marks = helper('container').marks();

  const bookmarkletCss = is_bookmarklet()
    ? `<link rel="stylesheet" type="text/css" href="${staticUrl}/style/bookmarklet.css">`
    : '';

  const feedLinks = marks
    ? `<link rel="alternate" type="application/atom+xml" href="${absolute_url(request_url() + '?format=atom')}">
<link rel="alternate" type="application/rss+xml" href="${absolute_url(request_url() + '?format=rss')}">`
    : '';

  return `<!doctype html>
<html style="background:url(${staticUrl}/style/img/background.png) top left repeat">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>${brand()} | ${strip_tags(title())}</title>
<link rel="icon" type="image/png" href="${staticUrl}/img/favicon_${domainValue}.png">
<link rel="stylesheet" type="text/css" href="${staticUrl}/components/bootstrap.css/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="${staticUrl}/components/bootstrap.css/css/bootstrap-responsive.css">
<link rel="stylesheet" type="text/css" href="${staticUrl}/components/select2/select2.css">
<link rel="stylesheet" type="text/css" href="${staticUrl}/style/bm.css">
${bookmarkletCss}
${feedLinks}
</head>
<body class="${domainValue}">

<div class="container">

${partial('top')}

<div id="layout" class="${section()}">

<div id="title-bar">
  <h1>${title()}</h1>
</div> <!-- /#title-bar -->

${content}

</div> <!-- /#layout -->

<div id="footer">
  <p>
  <span class="separator">Feedback: <a href="http://twitter.com/blogmarks">@blogmarks</a> on Twitter</span>
  <span class="separator">Designed &amp; supported by <a href="http://www.upian.com/">Upian</a></span>
  <span class="separator">Operated by <a href="http://hodierne.net/">Hodierne Ventures</a></span>
  <span>&copy; 2004-24 the Blogmarks team (blgmrks04tbf)</span>
 </p>
</div> <!-- /#footer -->

</div> <!-- /#container -->

<script src="${staticUrl}/components/jquery/dist/jquery.js"></script>
<script src="${staticUrl}/components/bootstrap.css/js/bootstrap.js"></script>
<script src="${staticUrl}/components/jquery-pjax/jquery.pjax.js"></script>
<script src="${staticUrl}/components/jquery.scrollTo/jquery.scrollTo.js"></script>
<script src="${staticUrl}/components/select2/select2.js"></script>
<script src="${staticUrl}/js/jquery-plugins.js"></script>
<script src="${staticUrl}/js/bm.js"></script>

</body>
</html>`;
};
