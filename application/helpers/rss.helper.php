<?php namespace Blogmarks\Helper;

class Rss
{

  static function seq()
  {
    echo '<rdf:Seq>';
    $marks = helper('container')->marks();
    foreach ($marks['items'] as $mark)
      echo "\n" . '      <rdf:li resource="' . arg($mark->url) . '"/>';
    echo '</rdf:Seq>';
  }

  static function marks()
  {
    $marks = helper('container')->marks();
    foreach ($marks['items'] as $mark) {
      self::item($mark);
    }
  }

  static function item($mark)
  {
?>
<item rdf:about="<?= text($mark->url) ?>">
  <title><?= text($mark->title) ?></title>
  <link><?= text($mark->url) ?></link>
  <description>TODO</description>
  <dc:date><?= text($mark->published) ?></dc:date>
  <dc:creator><?= text($mark->author->name) ?></dc:creator>
  <dc:subject>todo</dc:subject>
  <content:encoded><![CDATA[<?= view('partials/mark', ['domain' => domain(), 'mark' => $mark]) ?>]]></content:encoded>
</item>
<?php
  }

}

return replaceable('rss', single('Blogmarks\Helper\Rss'));
