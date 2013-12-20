<?php

return function() {
  if (!session_id()) {
    session_start();
  }
};
