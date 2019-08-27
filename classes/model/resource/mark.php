<?php namespace blogmarks\model\resource;

use
datetime,
datetimezone;

use
amateur\model\cache;

class mark extends \blogmarks\model\resource
{

  use
  \amateur\magic\dynamic_properties;

  function is_public()
  {
    return $this->visibility == 0;
  }

  function is_private()
  {
    return $this->visibility == 1;
  }

  function classname($user = null)
  {
    $classname = $this->visibility == 1 ? 'mark private' : 'mark';
    if (is_object($user) && $user->id == $this->user_id) {
      $classname .= ' own';
    }
    return $classname;
  }

  function user_id()
  {
    return (int)$this->attribute('author');
  }

  function user()
  {
    return $this->attribute('user') ?: $this->table('users')->get($this->user_id);
  }

  function author()
  {
    return $this->user;
  }

  function link_id()
  {
    return (int)$this->attribute('related');
  }

  function related()
  {
    return $this->table('links')->get($this->link_id);
  }

  function tags()
  {
    return $this->attribute('tags') ?: $this->table('marks_tags')->from_mark($this);
  }

  function public_tags()
  {
    return array_filter($this->tags, function($tag) { return !$tag->isHidden; });
  }

  function private_tags()
  {
    return array_filter($this->tags, function($tag) { return $tag->isHidden; });
  }

  function text()
  {
    if ($this->contentType == 'html') {
      return (new \Markdownify\Converter)->parseString($this->content);
    }
    return $this->content;
  }

  function published()
  {
    return new datetime($this->attribute('published'), new datetimezone('Europe/Paris'));
  }

  function updated()
  {
    return new datetime($this->attribute('updated'), new datetimezone('Europe/Paris'));
  }

  function screenshot()
  {
    if (!$screenshot = $this->attribute('screenshot')) {
      $screenshot = $this->internal_screenshot();
      if (empty($screenshot)) {
        $screenshot = $this->default_screenshot();
      }
      $this->cache_attribute('screenshot', $screenshot);
    }
    if (flag('rewrite_screenshot_url')) {
      $screenshot = str_replace('http://blogmarks.net/', absolute_url('/'), $screenshot);
    }
    return $screenshot;
  }

  function internal_screenshot()
  {
    $row = $this->table('screenshots')->for_mark($this);
    if ($row) {
      return $row['url'];
    }
  }

  function default_screenshot()
  {
    $parsed_url = parse_url($this->url);
    if (!empty($parsed_url['host'])) {
      return 'https://api.miniature.io/?url=' . $parsed_url['host'];
    }
    $n = substr($this->attribute('id'), -1) + 1;
    return absolute_url("/img/haikus/$n.gif");
  }

  function url()
  {
    if (!$url = $this->attribute('url')) {
      $url = $this->related->href;
      $this->cache_attribute('url', $url);
    }
    return $url;
  }

  function cache_attribute($key, $value)
  {
    $this->attributes[$key] = $value;
    $cache_key = $this->table('marks')->cache_key('id', $this->id);
    if ($row = cache::get($cache_key)) {
      $row[$key] = $value;
      cache::set($cache_key, $row);
    }
  }

}
