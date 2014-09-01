<?php namespace blogmarks\model;

class tags
{

  use
  \amateur\magic\closurable_methods,
  \blogmarks\magic\registry;

  function __construct()
  {
    $this->table = $this->table('tags');
    $this->feed  = $this->feed('tags');
  }

  # Collections

  function latests($params = [])
  {
    $query = $this->table->query_latests;
    return $this->feed->query("tags_public", $query, $params);
  }

  function related_with($tag, $params = [])
  {
    $query = $this->table->query_related_with->__use($tag);
    return $this->feed->query("tags_tag_{$tag->id}_public", $query, $params);
  }

  function from_user($user, $params = [])
  {
    $query = $this->table->query_from_user->__use($user, false);
    return $this->feed->query("tags_user_{$user->id}_public", $query, $params);
  }

  function from_user_related_with($user, $tag, $params = [])
  {
    $query = $this->table->query_from_user_related_with->__use($user, $tag, false);
    return $this->feed->query("tags_user_{$user->id}_tag_{$tag->id}_public", $query, $params);
  }

  function private_from_user($user, $params = [])
  {
    $query = $this->table->query_from_user->__use($user, true);
    return $this->feed->query("tags_user_{$user->id}_private", $query, $params);
  }

  function private_from_user_related_with($user, $tag, $params = [])
  {
    $query = $this->table->query_from_user_related_with->__use($user, $tag, true);
    return $this->feed->query("tags_user_{$user->id}_tag_{$tag->id}_private", $query, $params);
  }

  # Search

  function public_search($params = [])
  {
    $params = $params + ['limit' => 100];
    $tags = $this->latests(['limit' => null] + $params);
    return array_slice($tags, 0, $params['limit']);
  }

  function private_search_from_user($user, $params = [])
  {
    $params = $params + ['limit' => 100];
    $tags = $this->private_from_user($user, ['limit' => null] + $params);
    return array_slice($tags, 0, $params['limit']);
  }

}
