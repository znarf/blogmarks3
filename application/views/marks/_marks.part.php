<?php
$marks = isset($marks) ? $marks : helper('container')->marks();
$domain = domain();
?>

<?php if (count($marks['items']) > 0) : ?>

<?php foreach (helper('grouper')->group($marks['items']) as $group => $items) : ?>

<h2><span><?= $group ?></span></h2>

<?php foreach ($items as $item) : ?>

<?= view('partials/mark', ['domain' => $domain, 'mark' => $item]) ?>

<?php endforeach ?>

<?php endforeach ?>

<?php if ($marks['params']['limit'] == count($marks['items'])) : // loose way to know if there is more marks to load ?>
<div id="pagination">
    <a rel="next" class="page more" href="?before=<?= strtotime($item->published) ?>">more</a>
</div> <!-- /#pagination -->
<?php else : // only if before or offset is passed as parameter  ?>
   <h2><span>The End (A)</span></h2>
<?php endif ?>

<?php else : ?>

  <h2><span>The End (B)</span></h2>

  <!--
  <div>
    <p>No Result.</p>
  </div>
  -->

<?php endif ?>
