<?php $marks = helper('container')->marks() ?>
<feed
  xmlns="http://www.w3.org/2005/Atom"
  xmlns:bm="http://blogmarks.net/ns/"
  xmlns:openSearch="http://a9.com/-/spec/opensearch/1.1/"
  xmlns:app="http://www.w3.org/2007/app"
  xmlns:activity="http://activitystrea.ms/spec/1.0/">
<id>bm-marks-tags:1293</id>
<title><?= strip_tags(title()) ?></title>
<updated>2013-05-30T19:50:00Z</updated>
<openSearch:totalResults>5676</openSearch:totalResults>
<openSearch:startIndex>0</openSearch:startIndex>
<openSearch:itemsPerPage>30</openSearch:itemsPerPage>
<link rel="alternate" type="text/html" href="<?= web_url(url()) ?>" title="<?= strip_tags(title()) ?>"/>
<?php foreach ($marks['items'] as $mark) : ?>
<entry>
  <id>tag:blogmarks.net,$tag</id>
  <title><?= text($mark->title) ?></title>
  <updated><?= text($mark->updated) ?></updated>
  <published><?= text($mark->published) ?></published>
  <author>
    <name><?= text($mark->author->name) ?></name>
    <uri><?= text($mark->author->url) ?></uri>
  </author>
  <link href="<?= text($mark->url) ?>"/>
  <link rel="related" href="<?= text($mark->url) ?>"/>
  <link rel="alternate" href="<?= text($mark->url) ?>" type="text/html"/>
<?php if ($screenshot = $mark->screenshot) : ?>
<?php $type = strpos($screenshot, '.jpg') || strpos($screenshot, 'open.thumbshots.org') ? 'image/jpg' : 'image/png' ?>
  <link rel="enclosure" href="<?= arg($screenshot) ?>" type="<?= arg($type) ?>"/>
<?php endif ?>
<?php if ($content = $mark->content) : ?>
  <content type="<?= $mark->contentType ?>"><![CDATA[<?= $mark->content ?>]]></content>
<?php endif ?>
<?php foreach ($mark->tags as $tag) : ?>
<?php $scheme = $tag->isHidden ? $mark->author->url . '/private-tag' : 'http://blogmarks.net/tag/' ?>
<?php $term = urlencode($tag->label) ?>
  <category scheme="<?= arg($scheme) ?>" term="<?= arg($term) ?>" label="<?= arg($tag->label) ?>"/>
<?php endforeach ?>
<?php if ($mark->is_private()) : ?>
  <bm:isPrivate>true</bm:isPrivate>
<?php endif ?>
  <activity:verb>http://activitystrea.ms/schema/1.0/post</activity:verb>
  <activity:object>
    <activity:object-type>http://activitystrea.ms/schema/1.0/bookmark</activity:object-type>
  </activity:object>
</entry>
<?php endforeach ?>
</feed>