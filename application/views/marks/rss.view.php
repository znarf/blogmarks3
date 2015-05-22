<?php
$marks = helper('container')->marks();
?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel rdf:about="<?= api_url(request_url()) . '?format=rss' ?>">
  <title><?= strip_tags(title()) ?></title>
  <link><?= web_url(request_url()) ?></link>
  <description></description>
  <items>
    <rdf:Seq>
<?php foreach ($marks['items'] as $mark) : ?>
      <rdf:li resource="<?= arg($mark->url) ?>"/>
<?php endforeach ?>
    </rdf:Seq>
  </items>
</channel>
<?php $mark_partial_args = mark_partial_args(); ?>
<?php foreach ($marks['items'] as $mark) : ?>
<item rdf:about="<?= text($mark->url) ?>">
  <title><?= text($mark->title) ?></title>
  <link><?= text($mark->url) ?></link>
  <description><?= text($mark->content) ?></description>
  <dc:date><?= date(DATE_W3C, strtotime($mark->published . ' Europe/Berlin')) ?></dc:date>
  <dc:creator><?= text($mark->author->name) ?></dc:creator>
  <dc:subject><?= text(implode(', ', $mark->public_tags)) ?></dc:subject>
  <content:encoded><![CDATA[<?php
  partial('mark', ['mark' => $mark] + $mark_partial_args)
  ?>]]></content:encoded>
</item>
<?php endforeach ?>
</rdf:RDF>