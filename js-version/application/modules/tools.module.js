module.exports = function () {
  domain('my');
  title(_('Tools'));

  check_authenticated();
  const user = authenticated_user();

  section('tools');

  let matches;

  if (url_is('/my/tools')) {
    return redirect('/my/tools,bookmarklet');
  } else if (url_is('/my/tools,empty')) {
    if (is_post()) {
      check_token('tools_empty', get_param('token'));
      model('marks').delete_from_user(user);
      flash_message('Success! All marks and tags deleted from account.');
    }
    return render('tools/empty', { token: generate_token('tools_empty') });
  } else if (url_is('/my/tools,import')) {
    if (is_post()) {
      check_token('tools_import', get_param('token'));
      const results = [];
      const file = uploaded_file();
      const importer = helper('importer').start(user);
      const marks = importer.parse(file);
      for (const mark of marks) {
        try {
          importer.insert(mark);
          results.push([mark.title, 200, 'Ok']);
        } catch (e) {
          results.push([mark.title, e.getCode(), e.getMessage()]);
        }
      }
      importer.finish();
      return render('tools/import', { action: 'import', results });
    }
    return render('tools/import', {
      action: 'import',
      token: generate_token('tools_import')
    });
  } else if (url_is('/my/tools,export')) {
    if (get_bool('download')) {
      title('My Export');
      request_format('atom');
      set_param('export', true);
      ini_set('memory_limit', -1);
      helper('container').marks(model('marks').private_from_user.__use(user, { limit: -1 }));
      return render('marks');
    }
    return render('tools/export', { action: 'export' });
  } else if ((matches = url_match('/my/tools,*'))) {
    const action = matches[1];
    return render('tools/' + action);
  }

  return unknown_url();
};
