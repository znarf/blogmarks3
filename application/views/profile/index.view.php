<div id="content">
  <div id="content-inner">

    <ul class="bm-menu">
    <?php

    $categories = [
      'general' => _('General'),
    ];

    foreach ($categories as $category => $label) {
        $class = $action == $category ? "$category selected" : $category;
        echo
        '<li class="' . $class . '"><a href="' . relative_url("/my/profile,{$category}") . '">' .
          '<span>' . $label . '</span>' .
        '</a></li>' . "\n";
    }

    ?>
    </ul>

  </div>
</div> <!-- /#content -->

<div id="right-bar">
  <div id="right-bar-inner">

    <?= view('partials/notification') ?>

    <?php
    switch ($action) {
      case 'general':
        echo view('profile/general', ['fullname' => $user->name, 'email' => $user->email, 'username' => $user->login]);
        break;
    }
    ?>

  </div>
</div> <!-- /#right-bar -->
