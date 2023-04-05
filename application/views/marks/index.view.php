<?php partial('search') ?>

<?php $marks = isset($marks) ? $marks : helper('container')->marks(); ?>

<?php if ($marks['total'] === 0) helper('sidebar')->empty() ?>

<div id="content" class="<?= helper('sidebar')->is_empty() ? 'fullwidth' : '' ?>">
  <div id="content-inner">

    <?php partial('notification') ?>

    <div class="marks-list">

      <?php partial('marks') ?>

    </div>

  </div>
</div> <!-- /#content -->

<div id="right-bar">

  <?php helper('sidebar')->render(); ?>

</div> <!-- /#right-bar -->
