<?php namespace blogmarks;

function start_session()
{
  if (!session_id()) {
    session_start();
  }
}
