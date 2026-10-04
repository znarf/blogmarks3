#!/usr/bin/env php
<?php

use
amateur\model\cache,
amateur\model\db,
amateur\model\runtime,
amateur\model\table;

require_once dirname(__DIR__) . '/bootstrap.php';

replaceable('request_host', function () { return 'localhost:8002'; });

$screenshot_extension = 'png';
$screenshot_base_dir = root_dir . '/public/screenshots/';
$screenshot_base_url = absolute_url('/screenshots/');

$user = table('users')->get_one('login', 'znarf');
$marks = model('marks')->from_user($user, ['limit' => 1000]);

foreach ($marks['items'] as $mark) {
  error_log("Checking {$mark->url}");

  $screenshot = table('screenshots')->for_mark($mark);
  if (!empty($screenshot['url'])) {
    error_log("Existing {$screenshot['url']}");
    continue;
  }

  $parsed_url = parse_url($mark->url);
  if (empty($parsed_url['host'])) {
    continue;
  }

  $folder = date("Y/m/d/" , time() - date('Z') );
  $relative = $folder . md5($mark->url) . '.' . $bm_screenshot_extension;
  $screenshot_absolute_path = $screenshot_base_dir . $relative;
  $screenshot_absolute_url = $screenshot_base_url . $relative;

  if (!file_exists($screenshot_absolute_path)) {
    $parameters = [
      'url'    => $parsed_url['host'],
      'width'  => 112,
      'height' => 83,
      'token'  => flag('miniature_api_key'),
    ];
    $screenshot_api_url = 'https://api.miniature.io/' . '?' . http_build_query($parameters);

    // $parameters = [
    //   'url'    => $mark->url,
    //   'width'  => 112,
    // ];

    // $screenshot_api_url = 'https://api.thumbnail.ws/api/ab264f9ceea81bfaa0803a670001777f487268a1f197/thumbnail/get' . '?' . http_build_query($parameters);

    error_log("Asking {$screenshot_api_url}");
    $image = file_get_contents($screenshot_api_url);
    if (!$image) {
      continue;
    }

    $size = strlen($image);
    error_log("Size {$size}");
    if ($size <= 1252 || $size === 2185 || $size === 1857) {
       error_log("Suspicious size");
       continue;
    }

    if (!is_dir($screenshot_dir . $folder)) {
      error_log("Creating {$folder}");
      mkdir($screenshot_dir . $folder, 0777, true);
    }

    error_log("Writing {$screenshot_absolute_path}");
    file_put_contents($screenshot_absolute_path, $image);
    chmod($screenshot_absolute_path, 0777);
  }

  $params = ['status' => 1, 'generated' => db::now(), 'url' => $screenshot_absolute_url];
  if ($screenshot) {
    $screenshot->update($params);
  } else {
    table('screenshots')->create($params + ['link' => $mark->link_id]);
  }
}
