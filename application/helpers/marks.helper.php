<?php namespace blogmarks\helper;

class marks
{

  use \closurable_methods;

  static function latest_marks()
  {
    # With feed
    return helper('feed')->marks(
      "feed_marks",
      model('marks')->query_latest_ids_and_ts
    );
    # With search
    # return helper('search')->search();
  }

  static function marks_with_tag($tag)
  {
    # With feed
    return helper('feed')->marks(
      "feed_marks_tag_{$tag->id}",
      model('marks')->query_ids_and_ts_with_tag->__use($tag)
    );
    # With search
    # return helper('search')->search(['tag' => $tag]);
  }

  static function marks_with_tags($tags)
  {
    # Only available with search helper
    return helper('search')->search(['tags' => $tags]);
  }

  static function marks_from_user($user)
  {
    # With feed
    return helper('feed')->marks(
      "feed_marks_user_{$user->id}",
      model('marks')->query_ids_and_ts_from_user->__use($user)
    );
    # With search
    # return helper('search')->search(['user' => $user]);
  }

  static function marks_from_user_with_tag($user, $tag)
  {
    # Only available with search helper
    return helper('search')->search(['user' => $user, 'tag' => $tag]);
  }

  static function private_marks_from_user($user)
  {
    # With feed
    return helper('feed')->marks(
      "feed_marks_my_{$user->id}",
      model('marks')->query_ids_and_ts_from_user->__use($user, ['private' => true])
    );
    # With search
    # return helper('search')->search(['user' => $user, 'private' => true]);
  }

  static function private_marks_from_user_with_tag($user, $tag)
  {
    # Feed
    return helper('feed')->marks(
      "feed_marks_my_{$user->id}_tag_{$tag->id}",
      model('marks')->query_ids_and_ts_from_user_with_tag->__use($user, $tag, ['private' => true])
    );
    # With search
    # return helper('search')->search(['user' => $user, 'tag' => $tag, 'private' => true]);
  }

  static function private_marks_from_user_with_tags($user, $tags)
  {
    # Only available with search helper
    return helper('search')->search(['user' => $user, 'tags' => $tags, 'private' => true]);
  }

}

return new marks;
