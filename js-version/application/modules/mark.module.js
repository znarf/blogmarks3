module.exports = function () {
  let matches;
  check_authenticated();
  const user = authenticated_user();

  const as_params = function (mark) {
    return {
      url: mark.url,
      title: mark.title,
      description: mark.text,
      visibility: mark.visibility,
      tags: mark.public_tags.join(', '),
      private_tags: mark.private_tags.join(', ')
    };
  };

  const request_params = function () {
    return get_parameters([
      'url',
      'title',
      'description',
      'visibility',
      'tags',
      'private_tags'
    ]);
  };

  if (url_is('/my/marks,new')) {
    title(_('New Mark'));
    if (is_post()) {
      if (get_bool('save')) {
        check_parameters([
          'token',
          'url',
          'title',
          'description',
          'visibility',
          'tags',
          'private_tags'
        ]);
        check_token('new_mark', get_param('token'));
        model('marks').create(user, request_params());
        flash_message(_('Mark Successfully Added.'));
      }
      if (is_bookmarklet()) {
        return render('marks/close');
      }
      return redirect('/my/marks');
    }
    if (has_param('url')) {
      const link = table('links').get_one('href', get_param('url'));
      let mark;
      if (link && (mark = user.mark_with_link(link))) {
        flash_message('This URL is already in your marks.');
        redirect(
          '/my/marks/' +
            mark.id +
            ',edit' +
            (is_bookmarklet() ? '?bookmarklet=1' : '')
        );
      }
    }
    const params = request_params();
    Object.assign(params, { token: generate_token('new_mark') });
    return render('marks/form-modal', params);
  } else if ((matches = url_match('/my/marks/*,edit'))) {
    title(_('Edit Mark'));
    const mark = helper('target').mark(matches[1]);
    check_authenticated_user(mark.author);
    if (is_modal()) {
      const params = as_params(mark);
      Object.assign(params, { referer: referer(), token: generate_token('update_mark') });
      return html(view('marks/form-modal', params));
    }
    if (is_post()) {
      if (get_bool('save')) {
        check_parameters([
          'token',
          'url',
          'title',
          'description',
          'visibility',
          'tags',
          'private_tags'
        ]);
        check_token('update_mark', get_param('token'));
        try {
          model('marks').update(mark, request_params());
        } catch (e) {
          response_code(e.getCode());
          flash_message(e.getMessage());
          const params = request_params();
          Object.assign(params, {
            referer: get_param('referer'),
            token: generate_token('update_mark')
          });
          return render('marks/form-modal', params);
        }
      }
      if (is_bookmarklet()) {
        return render('marks/close');
      }
      return redirect(get_param('referer', '/my/marks') + '#mark' + mark.id);
    }
    const params = as_params(mark);
    Object.assign(params, { referer: referer(), token: generate_token('update_mark') });
    return render('marks/form-modal', params);
  } else if ((matches = url_match('/my/marks/*,delete'))) {
    title(_('Delete Mark'));
    const mark = helper('target').mark(matches[1]);
    check_authenticated_user(mark.author);
    if (is_modal()) {
      const params = { referer: referer(), token: generate_token('delete_mark') };
      return html(view('marks/delete', params));
    }
    if (is_post()) {
      if (get_bool('delete')) {
        check_token('delete_mark', get_param('token'));
        model('marks').delete(mark);
        flash_message(_('Mark Successfully Deleted.'));
      }
      return redirect(get_param('referer', '/my/marks'));
    }
    const params = { referer: referer(), token: generate_token('delete_mark') };
    return render('marks/delete', params);
  }

  return unknown_url();
};
