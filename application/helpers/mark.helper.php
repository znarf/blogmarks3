<?php namespace blogmarks\helper;

class mark
{

  static function params()
  {
    return [
      'url'         => get_param('url'),
      'title'       => get_param('title'),
      'description' => get_param('description', get_param('summary')),
      'visibility'  => get_int('visibility'),
      'tags'        => get_param('tags'),
    ];
  }

  static function as_params($mark)
  {
    if ($mark->contentType == 'html') {
      require_once root_dir . '/lib/markdownify/markdownify.php';
      $markdownify = new \markdownify;
      $content = $markdownify->parseString($mark->content);
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
  }

  static function new_mark()
  {
    $user = authenticated_user();
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
    model('marks_tags')->tag_mark($mark, explode(',', get_param('tags')));
    # Index Mark
    helper('feed')->index($mark);
    helper('search')->index($mark);
    # Return Mark
    return $mark;
  }

  static function update($mark)
  {
    $user = authenticated_user();
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
    model('marks_tags')->tag_mark($mark, explode(',', get_param('tags')));
    # Re-index Mark
    helper('feed')->index($mark);
    helper('search')->index($mark);
    # Return Mark
    return $mark;
  }

  static function delete($mark)
  {
    # Check CSRF token
    check_token('delete_mark', get_param('token'));
    # Un-index Mark (tags might be unavailable after mark is deleted)
    helper('feed')->unindex($mark);
    helper('search')->unindex($mark);
    # Delete Tags
    model('marks_tags')->delete(['mark_id' => $mark->id]);
    # Delete Mark
    model('marks')->delete($mark);
  }

}

return new mark;
