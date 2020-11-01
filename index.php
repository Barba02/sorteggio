<?php
  require 'include/config.inc.php';

  echo '<pre><code>';

  $file = prepare();
  
  if ($file) {
    execute($file, true);
  }

  echo '</code></pre>';
