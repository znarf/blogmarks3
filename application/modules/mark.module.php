<?php

helper('form');

# These helpers function doesn't deserve their own helper
replaceable('is_modal', function() { return get_param('modal'); } );
replaceable('is_bookmarklet', function() { return get_param('bookmarklet', get_param('mini')); } );
replaceable('close_bookmarklet', function() { return render('marks/close'); } );

# User should be authenticated at this point
check_authenticated();
$user = authenticated_user();

# Default Layout
if (is_bookmarklet()) { $app->default_layout = 'bookmarklet'; }

## Routing ##

if (url_is('/my/marks,new')) {
  title('New Mark');
  if (is_post()) {
    if (get_bool('save')) {
      $mark = helper('mark')->new();
      flash_message('Mark Successfully Added.');
    }
    if (is_bookmarklet()) {
      return close_bookmarklet();
    }
    return redirect('/my/marks');
  }
  if (has_param('url')) {
    # Redirect if user already has mark
    $link = model('links')->get_one('href', get_param('url'));
    if ($link && $mark = $user->mark_with_link($link)) {
      flash_message("This URL is already in your marks.");
      redirect( '/my/marks/' . $mark->id . ',edit' . (is_bookmarklet() ? '?bookmarklet=1' : '') );
    }
  }
  $params = helper('mark')->params() + ['token' => generate_token('new_mark')];
  return render('marks/form-modal', $params);
}

elseif ($matches = url_match('/my/marks/*,edit')) {
  title('Edit Mark');
  $mark = helper('target')->mark($matches[1]);
  check_authenticated_user($mark->author);
  # Need to be before is_post because it might be a POST itself
  if (is_modal()) {
    $params  = helper('mark')->as_params($mark);
    $params += ['referer' => referer(), 'token' => generate_token('update_mark')];
    return ok(view('marks/form-modal', $params));
  }
  if (is_post()) {
    if (get_bool('save')) {
      try {
        helper('mark')->update($mark);
        # flash_message('Mark Successfully Updated.');
      }
      catch (http_exception $e) {
        status( $e->getCode() );
        flash_message( $e->getMessage() );
        $params  = helper('mark')->params();
        $params += ['referer' => get_param('referer'), 'token' => generate_token('update_mark')];
        return render('marks/form-modal', $params);
      }
    }
    if (is_bookmarklet()) {
      return close_bookmarklet();
    }
    return redirect(get_param('referer', '/my/marks') . '#mark' . $mark->id);
  }
  $params  = helper('mark')->as_params($mark);
  $params += ['referer' => referer(), 'token' => generate_token('update_mark')];
  return render('marks/form-modal', $params);
}

elseif ($matches = url_match('/my/marks/*,delete')) {
  title('Delete Mark');
  $mark = helper('target')->mark($matches[1]);
  check_authenticated_user($mark->author);
  # Need to be before is_post because it might be a POST itself
  if (is_modal()) {
    $params = ['referer' => referer(), 'token' => generate_token('delete_mark')];
    return ok(view('marks/delete', $params));
  }
  if (is_post()) {
    if (get_bool('delete')) {
      helper('mark')->delete($mark);
      flash_message('Mark Successfully Deleted.');
    }
    return redirect(get_param('referer', '/my/marks'));
  }
  $params = ['referer' => referer(), 'token' => generate_token('delete_mark')];
  return render('marks/delete', $params);
}

else {
  unknown_url();
}
