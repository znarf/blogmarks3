<?php

domain('my');

section('tools');

title('Tools');

check_authenticated();
$user = authenticated_user();

if (url_is('/my/tools')) {
  redirect('/my/tools,bookmarklets');
}

elseif ($matches = url_match('/my/tools,*')) {
  $action = $matches[1];
  if ($action == 'export' && get_bool('download')) {
    title('My Export');
    set_param('limit', -1);
    set_param('export', true);
    request_format('atom');
    helper('container')->marks( model('marks')->private_from_user->__use($user) );
    return render('marks');
  }
  render('tools/index', ['action' => $action]);
}

else {
  unknown_url();
}
