<?php
$export = get_bool('export');
$marks = helper('container')->marks();
$user = domain() == 'my' ? authenticated_user() : helper('target')->user();
?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:bm="http://blogmarks.net/ns/">
<id>tag:blogmarks.net,2013:marks</id>
<title><?= strip_tags(title()) ?></title>
<updated><?= date(DateTime::RFC3339) ?></updated>
<link rel="alternate" type="text/html" href="<?= web_url(url()) ?>" title="<?= strip_tags(title()) ?>"/>
<?php if ($user) : ?>
<author>
  <name><?= text($user->name) ?></name>
  <uri><?= text($user->url) ?></uri>
</author>
<?php endif ?>
<?php foreach ($marks['items'] as $mark) : ?>
<entry>
  <id>tag:blogmarks.net,2013:<?= text($mark->id) ?></id>
  <title><?= text($mark->title) ?></title>
  <updated><?= date(DateTime::RFC3339, strtotime($mark->updated)) ?></updated>
  <published><?= date(DateTime::RFC3339, strtotime($mark->published)) ?></published>
<?php if (!$user) : ?>
  <author>
    <name><?= text($mark->author->name) ?></name>
    <uri><?= text($mark->author->url) ?></uri>
  </author>
<?php endif ?>
<?php if (!$export) : ?>
  <link href="<?= text($mark->url) ?>"/>
<?php endif ?>
  <link rel="related" href="<?= text($mark->url) ?>"/>
<?php if (!$export && $screenshot = $mark->screenshot) : ?>
<?php $type = strpos($screenshot, '.jpg') || strpos($screenshot, 'open.thumbshots.org') ? 'image/jpg' : 'image/png' ?>
  <link rel="enclosure" href="<?= arg($screenshot) ?>" type="<?= arg($type) ?>"/>
<?php endif ?>
<?php if ($content = $mark->content) : ?>
  <content type="<?= $mark->contentType ?>"><![CDATA[<?= $content ?>]]></content>
<?php endif ?>
<?php foreach ($mark->tags as $tag) : ?>
<?php $scheme = $tag->isHidden ? $mark->author->url . '/private-tag' : 'http://blogmarks.net/tag/' ?>
<?php $term = urlencode($tag->label) ?>
  <category scheme="<?= arg($scheme) ?>" term="<?= arg($term) ?>" label="<?= arg($tag->label) ?>"/>
<?php endforeach ?>
<?php if ($mark->is_private()) : ?>
  <bm:isPrivate>1</bm:isPrivate>
<?php endif ?>
</entry>
<?php endforeach ?>
</feed>