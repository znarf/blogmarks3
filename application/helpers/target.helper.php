<?php namespace blogmarks;

class target
{

  function user($slug = null)
  {
      if ($slug) {
        $user = blogmarks::$registry['target']['user'] = blogmarks::table('users')->get_one('login', urldecode($slug));
        if (!$user) throw blogmarks::http_error(404, 'User not found');
      }
      if (isset(blogmarks::$registry['target']['user'])) {
        return blogmarks::$registry['target']['user'];
      }
  }

  function tag($slug = null)
  {
    if ($slug) {
      $tag = blogmarks::$registry['target']['tag'] = blogmarks::table('tags')->get_one('label', urldecode($slug));
      if (!$tag) throw blogmarks::http_error(404, 'Tag not found');
    }
    if (isset(blogmarks::$registry['target']['tag'])) {
      return blogmarks::$registry['target']['tag'];
    }
  }

  function mark($slug = null)
  {
    if ($slug) {
      $mark = blogmarks::$registry['target']['mark'] = blogmarks::table('marks')->get_one('id', urldecode($slug));
      if (!$mark) throw blogmarks::http_error(404, 'Mark not found');
    }
    if (isset(blogmarks::$registry['target']['mark'])) {
      return blogmarks::$registry['target']['mark'];
    }
  }

}

return new target;
