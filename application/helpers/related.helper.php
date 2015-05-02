<?php namespace blogmarks;

class related
{

  use
  \amateur\magic\closurable_methods;

  function active_users()
  {
    $container = helper('container');
    $marks = $container->marks();
    $users = [];
    foreach ($marks['items'] as $mark) {
      $user = $mark->user;
      if (empty($users[$user->id])) {
        $users[$user->id] = $user;
        $users[$user->id]->last_published = strftime('%d %B %Y %H:00', strtotime($mark->published));
      }
    }
    return array_values($users);
  }

}

return new related;
