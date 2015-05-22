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
      $query = $this->table('screenshots')
        ->select('url')
        ->where(['link' => $this->link_id, 'status' => 1])
        ->order_by('created DESC');
      $row = $query->fetch_one();
      $screenshot = $row ? $row['url'] : $this->default_screenshot();
      $this->cache_attribute('screenshot', $screenshot);
    }
    return $screenshot;
  }

  function default_screenshot()
  {
    $parsed_url = parse_url($this->url);
    if (!empty($parsed_url['host'])) {
      return 'http://open.thumbshots.org/image.pxf?url=' . $parsed_url['host'];
    }
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
