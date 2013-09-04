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

## Module ##

# TODO: bundle module with Amateur
require_once app_dir . '/classes/module.class.php';
$module = new \Blogmarks\Module;

$module->mark_params = function() {
  return [
    'url'         => get_param('url'),
    'title'       => get_param('title'),
    'description' => get_param('description', get_param('summary')),
    'visibility'  => get_int('visibility'),
    'tags'        => get_param('tags'),
  ];
};

$module->mark_as_params = function($mark) {
  if ($mark->contentType == 'html') {
    require_once root_dir . '/lib/markdownify/markdownify.php';
    $md = new Markdownify;
    $content = $md->parseString($mark->content);
  } else {
    $content = $mark->content;
  }
  # TODO: process tags and private tags
  return [
    'url'         => $mark->url,
    'title'       => $mark->title,
    'description' => $content,
    'visibility'  => $mark->visibility,
    'tags'        => implode(', ', $mark->tags),
  ];
};

$module->new_mark = function() use($user) {
  check_parameters(['token', 'url', 'title', 'description', 'visibility', 'tags']);
  # Check CSRF token
  check_token('new_mark', get_param('token'));
  # Get Link
  $link = model('links')->with_url(get_param('url'));
  if ($user->mark_with_link($link)) {
    throw http_error(400, 'Mark already exists.');
  }
  # Insert Mark
  $mark = model('marks')->create([
    'author'      => $user->id,
    'related'     => $link->id,
    'title'       => get_param('title'),
    'content'     => get_param('description'),
    'visibility'  => get_int('visibility')
  ]);
  # Insert Tags
  model('marks-tags')->tag_mark($mark, explode(',', get_param('tags')));
  # Index Mark
  helper('feed')->index($mark);
  # Return Mark
  return $mark;
};

$module->update_mark = function($mark) use($user) {
  check_parameters(['token', 'url', 'title', 'description', 'visibility', 'tags']);
  # Check CSRF token
  check_token('update_mark', get_param('token'));
  # Get Link
  if ($mark->url != get_param('url')) {
    $link = model('links')->with_url(get_param('url'));
    if ($user->mark_with_link($link)) {
      throw http_error(400, 'Mark already exists.');
    }
  } else {
    $link = $mark->related;
  }
  # Handle Content
  if ($mark->contentType == 'html') {
    $content = \Michelf\Markdown::defaultTransform(get_param('description'));
  }
  else {
    $content = get_param('description');
  }
  # Update Mark
  $mark = model('marks')->update($mark, [
    'related'     => $link->id,
    'title'       => get_param('title'),
    'content'     => $content,
    'visibility'  => get_int('visibility')
  ]);
  # Un-index Mark
  helper('feed')->unindex($mark);
  # Update Tags
  model('marks-tags')->tag_mark($mark, explode(',', get_param('tags')));
  # Re-index Mark
  helper('feed')->index($mark);
  # Return Mark
  return $mark;
};

$module->delete_mark = function($mark) {
  # Check CSRF token
  check_token('delete_mark', get_param('token'));
  # Un-index Mark (tags might be unavailable after mark is deleted)
  helper('feed')->unindex($mark);
  # Delete Tags
  model('marks-tags')->delete(['mark_id' => $mark->id]);
  # Delete Mark
  model('marks')->delete($mark);
};

## Routing ##

if (url_is('/my/marks,new')) {
  title('New Mark');
  if (is_post()) {
    if (get_bool('save')) {
      $mark = $module->new_mark();
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
  $params = $module->mark_params() + ['token' => generate_token('new_mark')];
  return render('marks/form-modal', $params);
}

elseif ($matches = url_match('/my/marks/*,edit')) {
  title('Edit Mark');
  $mark = helper('target')->mark($matches[1]);
  check_authenticated_user($mark->author);
  # Need to be before is_post because it might be a POST itself
  if (is_modal()) {
    $params  = $module->mark_as_params($mark);
    $params += ['referer' => referer(), 'token' => generate_token('update_mark')];
    return ok(view('marks/form-modal', $params));
  }
  if (is_post()) {
    if (get_bool('save')) {
      try {
        $module->update_mark($mark);
        # flash_message('Mark Successfully Updated.');
      }
      catch (HttpException $e) {
        status( $e->getCode() );
        flash_message( $e->getMessage() );
        $params  = $module->mark_params();
        $params += ['referer' => get_param('referer'), 'token' => generate_token('update_mark')];
        return render('marks/form-modal', $params);
      }
    }
    if (is_bookmarklet()) {
      return close_bookmarklet();
    }
    return redirect(get_param('referer', '/my/marks') . '#mark' . $mark->id);
  }
  $params  = $module->mark_as_params($mark);
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
      $module->delete_mark($mark);
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
