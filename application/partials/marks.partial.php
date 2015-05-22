<?php $marks = isset($marks) ? $marks : helper('container')->marks(); ?>

<?php if (count($marks['items']) > 0) : ?>

<?php $mark_partial_args = mark_partial_args(); ?>

<?php foreach (helper('grouper')->group($marks['items']) as $group => $items) : ?>

<h2><span><?= $group ?></span></h2>

<?php foreach ($items as $mark) : ?>

<?php partial('mark', ['mark' => $mark] + $mark_partial_args) ?>

<?php endforeach ?>

<?php endforeach ?>

<?php if (!empty($marks['next'])) : ?>
<div id="pagination">
  <?php
  if ($marks['params']['order'] == 'asc') {
    $more = ['order' => 'asc', 'after' => $marks['next']];
  }
  else {
    $more = ['order' => 'desc', 'before' => $marks['next']];
  }
  ?>
  <a rel="next" class="page more" href="?<?= http_build_query($more) ?>">more</a>
</div> <!-- /#pagination -->
<?php else : ?>
   <h2><span>The End</span></h2>
<?php endif ?>

<?php else : ?>

  <h2><span>The End (No more Result)</span></h2>

  <!--
  <div>
    <p>No Result.</p>
  </div>
  -->

<?php endif ?>
