#!/usr/bin/env php
<?php

# Take pending screenshots with Microlink (https://microlink.io/docs/guides/screenshot)
#
#   php workers/screenshot.worker.php          take pending screenshots
#   php workers/screenshot.worker.php <login>  also queue the user's marks without a screenshot
#
# The free tier allows 25 requests per day, run it from cron (e.g. hourly).
# Optional flags: microlink_api_key (pro endpoint), screenshot_limit (requests per run).

use
amateur\model\cache,
amateur\model\db;

require_once dirname(__DIR__) . '/bootstrap.php';

const screenshot_pending = 0;
const screenshot_done    = 1;
const screenshot_failed  = 2;

$limit = flag('screenshot_limit') ?: 25;
$base_dir = root_dir . '/public';

function microlink_screenshot($url)
{
  $parameters = [
    'url'             => $url,
    'screenshot'      => 'true',
    'meta'            => 'false',
    'type'            => 'jpeg',
    'viewport.width'  => 1024,
    'viewport.height' => 768,
  ];
  $endpoint = 'https://api.microlink.io/';
  $headers = [];
  if (flag('microlink_api_key')) {
    $endpoint = 'https://pro.microlink.io/';
    $headers[] = 'x-api-key: ' . flag('microlink_api_key');
  }
  $context = stream_context_create(['http' => [
    'header'        => $headers,
    'timeout'       => 60,
    'ignore_errors' => true,
  ]]);
  $body = @file_get_contents($endpoint . '?' . http_build_query($parameters), false, $context);
  $result = ['status' => 0, 'remaining' => null, 'image' => null];
  foreach ($http_response_header ?? [] as $header) {
    if (preg_match('#^HTTP/\S+ (\d+)#', $header, $matches)) {
      $result['status'] = (int)$matches[1];
    }
    elseif (preg_match('#^x-rate-limit-remaining: (\d+)#i', $header, $matches)) {
      $result['remaining'] = (int)$matches[1];
    }
  }
  $json = json_decode((string)$body, true);
  $image = $json['data']['screenshot']['url'] ?? null;
  # Only download images served by Microlink
  if ($image && preg_match('#^https://([a-z0-9-]+\.)*microlink\.io/#', $image)) {
    $result['image'] = $image;
  }
  return $result;
}

function save_thumbnail($image, $path)
{
  if (!is_dir(dirname($path))) {
    mkdir(dirname($path), 0755, true);
  }
  # Downsize to 2x the displayed size (112x83) when GD is available
  if (function_exists('imagecreatefromstring') && $source = @imagecreatefromstring($image)) {
    $thumbnail = imagescale($source, 224);
    return imagejpeg($thumbnail, $path, 80);
  }
  return file_put_contents($path, $image) !== false;
}

function clear_marks_cache($link_id)
{
  foreach (table('marks')->fetch_ids(['related' => $link_id]) as $mark_id) {
    cache::delete(table('marks')->cache_key('id', $mark_id));
  }
}

# Queue marks of the given user without a screenshot
if (!empty($argv[1])) {
  $user = table('users')->get_one('login', $argv[1]);
  if (!$user) {
    exit("Unknown user {$argv[1]}\n");
  }
  $marks = model('marks')->from_user($user, ['limit' => 1000]);
  foreach ($marks['items'] as $mark) {
    if (!table('screenshots')->fetch_one(['link' => $mark->link_id])) {
      table('screenshots')->ensure_entry_exists_for_mark($mark);
    }
  }
}

$pending = table('screenshots')
  ->select(['id', 'link'])
  ->where(['status' => screenshot_pending])
  ->order_by('created DESC')
  ->fetch_all();

$requests = 0;

foreach ($pending as $row) {
  $set = ['generated' => db::now()];

  # Reuse an existing screenshot of the same link
  $existing = table('screenshots')->for_mark((object)['link_id' => $row['link']]);
  if ($existing) {
    table('screenshots')->update(['id' => $row['id']], $set + ['status' => screenshot_done, 'url' => $existing['url']]);
    continue;
  }

  $link = table('links')->get($row['link']);
  if (!$link || !preg_match('#^https?://#', $link->href)) {
    table('screenshots')->update(['id' => $row['id']], $set + ['status' => screenshot_failed]);
    continue;
  }

  if ($requests >= $limit) {
    error_log("Limit of {$limit} requests reached");
    break;
  }
  $requests++;

  error_log("Asking Microlink for {$link->href}");
  $result = microlink_screenshot($link->href);

  # Quota exceeded or service unavailable, keep pending for next run
  if ($result['status'] == 429 || $result['status'] == 0 || $result['status'] >= 500) {
    error_log("Microlink unavailable (HTTP {$result['status']})");
    break;
  }

  $image = $result['image'] ? @file_get_contents($result['image']) : null;
  $relative = '/screenshots/' . gmdate('Y/m/d/') . md5($link->href) . '.jpg';
  if ($image && save_thumbnail($image, $base_dir . $relative)) {
    error_log("Saved {$relative}");
    table('screenshots')->update(['id' => $row['id']], $set + ['status' => screenshot_done, 'url' => $relative]);
    clear_marks_cache($row['link']);
  }
  else {
    error_log("Failed (HTTP {$result['status']})");
    table('screenshots')->update(['id' => $row['id']], $set + ['status' => screenshot_failed]);
  }

  if ($result['remaining'] === 0) {
    error_log("Microlink quota exhausted");
    break;
  }
}
