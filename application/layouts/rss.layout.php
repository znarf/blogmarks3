<?php
$user = authenticated_user();
$marks = helper('container')->marks();
?>
<rdf:RDF
  xmlns="http://purl.org/rss/1.0/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel rdf:about="<?= api_url(url()) . '?format=rss' ?>">
  <title><?= strip_tags(title()) ?></title>
  <link><?= web_url(url()) ?></link>
  <description></description>
  <items>
    <rdf:Seq>
<?php foreach ($marks['items'] as $mark) : ?>
      <rdf:li resource="<?= arg($mark->url) ?>"/>
<?php endforeach ?>
    </rdf:Seq>
  </items>
</channel>
<?php foreach ($marks['items'] as $mark) : ?>
<item rdf:about="<?= text($mark->url) ?>">
  <title><?= text($mark->title) ?></title>
  <link><?= text($mark->url) ?></link>
  <description>TODO</description>
  <dc:date><?= text($mark->published) ?></dc:date>
  <dc:creator><?= text($mark->author->name) ?></dc:creator>
  <dc:subject>todo</dc:subject>
  <content:encoded><![CDATA[<?=
  view('partials/mark', ['domain' => domain(), 'mark' => $mark, 'user' => $user])
  ?>]]></content:encoded>
</item>
<?php endforeach ?>
</rdf:RDF>