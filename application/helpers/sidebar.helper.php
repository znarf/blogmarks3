<?php

$sidebar = anonymous_class();

$sidebar->blocks = [];

$sidebar->register = function($title, $content) {
  $this->blocks[] = [
    'title' => $title,
    'content' => $content,
  ];
};

$sidebar->render = function() {
  foreach ($this->blocks as $block) {
    side_title(...$block['title']);
    echo is_callable($block['content']) ? $block['content']() : $block['content'];
  }
};

return $sidebar;
