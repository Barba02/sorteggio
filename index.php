<?php
  require 'include/config.inc.php';

  echo '<code>';

  $file = prepare();
  
  if ($file) {
    execute($file);
  }

  echo '</code>';
