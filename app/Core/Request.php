<?php

namespace App\Core;

class Request
{
  private $body;
  private $files;

  public function __construct()
  {
    $this->parseBody();
    $this->files = $_FILES;
  }

  private function parseBody()
  {
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

    if (stripos($contentType, 'application/json') !== false) {
      $this->body = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
      $this->body = $_POST;
    }
  }

  public function getBody()
  {
    return $this->body;
  }

  public function getFiles()
  {
    return $this->files;
  }

  public function input($key, $default = null)
  {
    return $this->body[$key] ?? $default;
  }

  public function file($key)
  {
    return $this->files[$key] ?? null;
  }
}
