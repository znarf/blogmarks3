<?php

require __DIR__ . '/../../vendor/autoload.php';

$css_files = array(
    "p-init.less",
    "p-extra.less",
    "p-bootstrap.less",
    "p-title-bar.less",
    "p-content.less",
    "p-markers.less",
    "p-marks.less",
    "p-pagination.less",
    "p-right-bar.less",
    "p-taglist.less",
    "p-footer.less",
    "p-tools.less",
    "p-new.less",
);

$merged = "";
foreach ($css_files as $file) {
    $file = __DIR__ . '/' . $file;
    $merged .= file_get_contents($file) . "\n";
}

$less = new lessc();

header('Content-Type:text/css');

echo $less->parse($merged);
