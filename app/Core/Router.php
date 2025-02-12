<?php

namespace App\Core;

use App\Traits\ResponseTrait;

class Router
{
  use ResponseTrait;
  protected array $routes = [];

  public function get(string $uri, array $action, array $middleware = [])
  {
    $this->routes['GET'][$uri] = compact('action', 'middleware');
  }

  public function post(string $uri, array $action, array $middleware = [])
  {
    $this->routes['POST'][$uri] = compact('action', 'middleware');
  }

  public function patch(string $uri, array $action, array $middleware = [])
  {
    $this->routes['PATCH'][$uri] = compact('action', 'middleware');
  }
  public function put(string $uri, array $action, array $middleware = [])
  {
    $this->routes['PUT'][$uri] = compact('action', 'middleware');
  }
  public function delete(string $uri, array $action, array $middleware = [])
  {
    $this->routes['DELETE'][$uri] = compact('action', 'middleware');
  }

  public function resolve(string $requestUri, string $requestMethod)
  {
    $uri = rtrim(urldecode(parse_url($requestUri, PHP_URL_PATH)), '/');  // Decode URL to handle @ and .
    $method = strtoupper($requestMethod);

    // Adjust URI based on the base path
    if (strpos($uri, BASE_PATH) === 0) {
      $uri = substr($uri, strlen(BASE_PATH));
    }

    foreach ($this->routes[$method] ?? [] as $route => $routeConfig) {
      $pattern = preg_replace('#\{(\w+)\}#', '([^/]+)', $route);
      $pattern = "#^" . $pattern . "$#";

      if (preg_match($pattern, $uri, $matches)) {
        array_shift($matches); // Remove full match
        return $this->handleRoute($routeConfig, $matches);
      }
    }

    if (strpos($uri, '/api') === 0) {
      $controller = 'App\Controllers\Controller';
      $method = 'apiNotFound';
    } else {
      $controller = 'App\Controllers\Controller';
      $method = 'notFound';
    }
    return $this->callAction([$controller, $method], []);


    //echo json_encode(['error' => 'Route not found']);
  }

  protected function handleRoute(array $routeConfig, array $params)
  {
    ['action' => $action, 'middleware' => $middleware] = $routeConfig;

    // Process middleware
    foreach ($middleware as $middlewareClass) {
      if (!class_exists($middlewareClass)) {
        throw new \Exception("Middleware class not found: {$middlewareClass}");
      }

      $middlewareInstance = new $middlewareClass();
      if (method_exists($middlewareInstance, 'handle')) {
        $middlewareResponse = $middlewareInstance->handle();
        if ($middlewareResponse === false) {
          // Stop execution if middleware fails
          return;
        }
      }
    }

    return $this->callAction($action, $params);
  }

  protected function callAction(array $action, array $params)
  {
    [$controller, $method] = $action;

    if (!class_exists($controller) || !method_exists($controller, $method)) {
      throw new \Exception("Controller or method not found: {$controller}@{$method}");
    }

    $controllerInstance = new $controller();
    return call_user_func_array([$controllerInstance, $method], $params);
  }
}
