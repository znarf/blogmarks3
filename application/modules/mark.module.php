<?php

# User should be authenticated at this point
check_authenticated();
$user = authenticated_user();

# Params

$as_params = function($mark) {
  # TODO: process tags and private tags
  return [
    'url'          => $mark->url,
    'title'        => $mark->title,
    'description'  => $mark->text,
    'visibility'   => $mark->visibility,
    'tags'         => implode(', ', $mark->public_tags),
    'private_tags' => implode(', ', $mark->private_tags),
  ];
};

$request_params = function() {
  return get_parameters(['url', 'title', 'description', 'visibility', 'tags', 'private_tags']);
};

# Create

if (url_is('/my/marks,new')) {
  title(_('New Mark'));
  if (is_post()) {
    if (get_bool('save')) {
      check_parameters(['token', 'url', 'title', 'description', 'visibility', 'tags', 'private_tags']);
      check_token('new_mark', get_param('token'));
      model('marks')->create($user, $request_params());
      flash_message(_('Mark Successfully Added.'));
    }
    if (is_bookmarklet()) {
      return render('marks/close');
    }
    return redirect('/my/marks');
  }
  # Redirect if user already has mark
  if (has_param('url')) {
    $link = table('links')->get_one('href', get_param('url'));
    if ($link && $mark = $user->mark_with_link($link)) {
      flash_message("This URL is already in your marks.");
      redirect( '/my/marks/' . $mark->id . ',edit' . (is_bookmarklet() ? '?bookmarklet=1' : '') );
    }
  }
  $params  = $request_params();
  $params += ['token' => generate_token('new_mark')];
  return render('marks/form-modal', $params);
}

# Update

elseif ($matches = url_match('/my/marks/*,edit')) {
  title(_('Edit Mark'));
  $mark = helper('target')->mark($matches[1]);
  check_authenticated_user($mark->author);
  # Need to be before is_post because it might be a POST itself
  if (is_modal()) {
    $params  = $as_params($mark);
    $params += ['referer' => referer(), 'token' => generate_token('update_mark')];
    return html(view('marks/form-modal', $params));
  }
  #
  if (is_post()) {
    if (get_bool('save')) {
      check_parameters(['token', 'url', 'title', 'description', 'visibility', 'tags', 'private_tags']);
      check_token('update_mark', get_param('token'));
      try {
        model('marks')->update($mark, $request_params());
        # flash_message('Mark Successfully Updated.');
      }
      catch (\amateur\core\exception $e) {
        response_code( $e->getCode() );
        flash_message( $e->getMessage() );
        $params  = $request_params();
        $params += ['referer' => get_param('referer'), 'token' => generate_token('update_mark')];
        return render('marks/form-modal', $params);
      }
    }
    if (is_bookmarklet()) {
      return render('marks/close');
    }
    return redirect(get_param('referer', '/my/marks') . '#mark' . $mark->id);
  }
  $params  = $as_params($mark);
  $params += ['referer' => referer(), 'token' => generate_token('update_mark')];
  return render('marks/form-modal', $params);
}

# Delete

elseif ($matches = url_match('/my/marks/*,delete')) {
  title(_('Delete Mark'));
  $mark = helper('target')->mark($matches[1]);
  check_authenticated_user($mark->author);
  # Need to be before is_post because it might be a POST itself
  if (is_modal()) {
    $params = ['referer' => referer(), 'token' => generate_token('delete_mark')];
    return html(view('marks/delete', $params));
  }
  if (is_post()) {
    if (get_bool('delete')) {
      check_token('delete_mark', get_param('token'));
      model('marks')->delete($mark);
      flash_message(_('Mark Successfully Deleted.'));
    }
    return redirect(get_param('referer', '/my/marks'));
  }
  $params = ['referer' => referer(), 'token' => generate_token('delete_mark')];
  return render('marks/delete', $params);
}

else {
  return unknown_url();
}
