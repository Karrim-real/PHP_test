<?php

// Original SPL Autoloader
spl_autoload_register(function ($class) {
  $class = str_replace('App\\', 'app\\', $class);
  $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';

  if (!file_exists($file)) {
    error_log("Autoloader could not find: {$file}");
  } else {
    error_log("Autoloader loading: {$file}");
    require_once $file;
  }
});
