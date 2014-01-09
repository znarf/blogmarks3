<ul class="bm-menu">
<?php

$categories = [
  'bookmarklet'  => _('Bookmarklet'),
  'import'       => _('Import'),
  'export'       => _('Export'),
  'empty'        => _('Empty')
];

foreach ($categories as $category => $label) {
    $class = $action == $category ? "$category selected" : $category;
    echo
    '<li class="' . $class . '"><a href="' . relative_url("/my/tools,$category") . '">' .
      '<span>' . $label . '</span>' .
    '</a></li>' . "\n";
}

?>
</ul>