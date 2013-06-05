<?php

domain('my');

section('tools');

title('Tools');

check_authenticated();
$user = authenticated_user();

if (url_is('/my/tools')) {
  redirect('/my/tools,bookmarklets');
}

else if ($matches = url_match('/my/tools,*')) {
  $action = $matches[1];
  if (is_post() && $action == 'empty') {
    action('empty');
  }
  layout(view('tools/index', ['action' => $action]));
}

else {
  unknown_url();
}
