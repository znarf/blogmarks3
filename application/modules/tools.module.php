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
  if ($action == 'export' && get_bool('download')) {
    title('My Export');
    set_param('limit', -1);
    set_param('export', true);
    request_format('atom');
    return send_marks( helper('marks')->private_marks_from_user->__use($user) );
  }
  layout(view('tools/index', ['action' => $action]));
}

else {
  unknown_url();
}
