<?php

function loadEnv($filePath)
{
  if (!file_exists($filePath)) {
    throw new Exception('.env file not found');
  }

  $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

  foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) {
      continue;
    }

    list($name, $value) = explode('=', $line, 2);

    $name = trim($name);
    $value = trim($value);

    if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
      putenv("$name=$value");
      $_ENV[$name] = $value;
      $_SERVER[$name] = $value;
    }
  }
}

// Load the environment variables
loadEnv(__DIR__ . '/../.env');



$ip = $_SERVER['REMOTE_ADDR'] ?? $_SERVER['SERVER_ADDR'];
$port = $_SERVER['REMOTE_PORT'] ?? $_SERVER['SERVER_PORT'];

if (($_SERVER['SERVER_NAME'] == 'localhost' && $_SERVER['SERVER_PORT'] === '80') ||
  ($ip === '127.0.0.1' && $port) === '80' ||  strpos($ip, '192.168.8') === 0
) {
  define('BASE_PATH', '/php/task');
} else {
  define('BASE_PATH', '');
}
