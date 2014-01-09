<?php
$marks = isset($marks) ? $marks : helper('container')->marks();
$user = authenticated_user();
$domain = domain();
?>

<?php if (count($marks['items']) > 0) : ?>

<?php foreach (helper('grouper')->group($marks['items']) as $group => $items) : ?>

<h2><span><?= $group ?></span></h2>

<?php foreach ($items as $item) : ?>

<?php partial('mark', ['domain' => $domain, 'user' => $user, 'mark' => $item]) ?>

<?php endforeach ?>

<?php endforeach ?>

<?php if ($marks['params']['limit'] == count($marks['items'])) : // loose way to know if there is more marks to load ?>
<div id="pagination">
  <?php
  if ($marks['params']['order'] == 'asc') {
    $more = ['order' => 'asc', 'after' => strtotime($item->published)];
  }
  else {
    $more = ['order' => 'desc', 'before' => strtotime($item->published)];
  }
  ?>
  <a rel="next" class="page more" href="?<?= http_build_query($more) ?>">more</a>
</div> <!-- /#pagination -->
<?php else : // only if before or offset is passed as parameter  ?>
   <h2><span>The End (Limit not reached)</span></h2>
<?php endif ?>

<?php else : ?>

  <h2><span>The End (No more Result)</span></h2>

  <!--
  <div>
    <p>No Result.</p>
  </div>
  -->

<?php endif ?>
