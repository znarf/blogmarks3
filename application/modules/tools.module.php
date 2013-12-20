<?php

domain('my');

title('Tools');

check_authenticated();
$user = authenticated_user();

section('tools');

if (url_is('/my/tools')) {
  redirect('/my/tools,bookmarklets');
}

elseif (url_is('/my/tools,empty')) {
  if (is_post()) {
    check_token('tools_empty', get_param('token'));
    model('marks')->delete_from_user($user);
  }
  render('tools/index', ['action' => 'empty', 'token' => generate_token('tools_empty')]);
}

elseif (url_is('/my/tools,import')) {
  if (is_post()) {
    check_token('tools_import', get_param('token'));
    $results = [];
    $file = uploaded_file();
    $importer = helper('importer')->start($user);
    $marks = $importer->parse($file);
    foreach ($marks as $mark) {
      try {
        $importer->insert($mark);
        $results[] = [$mark['title'], 200, 'Ok'];
      }
      catch (exception $e) {
        $results[] = [$mark['title'], $e->getCode(), $e->getMessage()];
      }
    }
    $importer->finish();
    return render('tools/index', ['action' => 'import', 'results' => $results]);
  }
  render('tools/index', ['action' => 'import', 'token' => generate_token('tools_import')]);
}

elseif (url_is('/my/tools,export')) {
  if (get_bool('download')) {
    title('My Export');
    request_format('atom');
    set_param('export', true);
    helper('container')->marks( model('marks')->private_from_user->__use($user,  ['limit' => -1]) );
    return render('marks');
  }
  render('tools/index', ['action' => 'export']);
}

elseif ($matches = url_match('/my/tools,*')) {
  $action = $matches[1];
  render('tools/index', ['action' => $action]);
}

else {
  unknown_url();
}
