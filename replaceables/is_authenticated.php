<?php namespace blogmarks;

function is_authenticated()
{
  return (bool) blogmarks::authenticated_user();
};
