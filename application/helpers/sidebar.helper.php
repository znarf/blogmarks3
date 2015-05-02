<?php namespace blogmarks\application\helpers;

class sidebar
{

  public $blocks = [];

  function register($title, $content)
  {
    $this->blocks[] = [
      'title' => $title,
      'content' => $content,
    ];
  }

  function render()
  {
    foreach ($this->blocks as $block) {
      side_title(...$block['title']);
      echo is_callable($block['content']) ? $block['content']() : $block['content'];
    }
  }

}

return new sidebar;
