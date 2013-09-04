<?php

domain('my');

section('tools');

title('Tools');

helper('form');

check_authenticated();
$user = authenticated_user();

if (url_is('/my/tools')) {
  redirect('/my/tools,bookmarklets');
}

else if ($matches = url_match('/my/tools,*')) {
  $action = $matches[1];
  if (is_post()) {
    switch ($action) {
      case 'export':
        check_token('export', get_param('token'));
        title('My Export');
        set_param('limit', -1);
        set_param('export', true);
        $app->request_format('atom');
        return $app->marks( helper('feed')->private_marks_from_user->__use($user) );
    }
  }
  layout(view('tools/index', ['action' => $action]));
}

else {
  unknown_url();
}
