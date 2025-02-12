<?php

namespace App\Controllers;

use App\Models\User;
use App\Traits\ResponseTrait;


class Controller
{
  use ResponseTrait;

  public function render($view, $data = [], $meta = null)
  {
    $header = __DIR__ . "/../../views/partials/header.php";
    $footer = __DIR__ . "/../../views/partials/footer.php";
    $path = __DIR__ . "/../../views/$view.php";
    if (!file_exists($path)) {
      throw new \Exception("View $view does not exist");
    }
    extract($data);
    ob_start();
    include($header);
    echo "<body>";
    echo "<div class='container'>";
    include($path);
    echo "</div>";
    include($footer);
    echo "</body>";
    return ob_get_clean();
  }

  public function notFound()
  {
    echo $this->render('errors/404');
  }

  public function apiNotFound()
  {
    return $this->errorResponse('Not found', 404);
  }

  public function isApi($requestUri)
  {
    $uri = rtrim(urldecode(parse_url($requestUri, PHP_URL_PATH)), '/');
    // Adjust URI based on the base path
    if (strpos($uri, BASE_PATH) === 0) {
      $uri = substr($uri, strlen(BASE_PATH));
    }
    return strpos($uri, '/api') === 0;
  }

  public function redirect($url, $error = null, $success = null)
  {
    if ($error) {
      $_SESSION['error'] = $error;
    }
    if ($success) {
      $_SESSION['success'] = $success;
    }
    header("Location: " . BASE_PATH . "/$url");
    exit;
  }
}
