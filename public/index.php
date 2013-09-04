<?php

/*
if (extension_loaded('xhprof')) {
  include_once '/Users/znarfor/Dev/xhprof/xhprof_lib/utils/xhprof_lib.php';
  include_once '/Users/znarfor/Dev/xhprof/xhprof_lib/utils/xhprof_runs.php';
  xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
  register_shutdown_function(function() {
    $profiler_namespace = 'bm-next';
    $xhprof_data = xhprof_disable();
    $xhprof_runs = new XHProfRuns_Default('/tmp');
    $run_id = $xhprof_runs->save_run($xhprof_data, $profiler_namespace);
    $profiler_url = sprintf('http://localhost/xhprof/xhprof_html/index.php?run=%s&source=%s', $run_id, $profiler_namespace);
    error_log($profiler_url);
  });
}
*/

define('root_dir', dirname(__DIR__));

define('app_dir', root_dir . '/application');

define('amateur_dir', root_dir . '/vendor/amateur');

require root_dir . '/vendor/autoload.php';

require_once amateur_dir . '/core/core.functions.php';
require_once amateur_dir . '/core/closure.functions.php';
require_once amateur_dir . '/core/replaceable.functions.php';

require_once app_dir . '/classes/app.class.php';
$app = core('app', new \Blogmarks\App);

// require_once amateur_dir . '/core/request.class.php';
// core('request', new \Amateur\Core\Request);

// require_once app_dir . '/classes/response.class.php';
// core('response', new \Blogmarks\Response);

require_once amateur_dir . '/amateur.dsl.php';
require_once amateur_dir . '/db.php';

include root_dir . '/config.php';

$app->start(app_dir);
