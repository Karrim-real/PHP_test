<?php

namespace App\Middleware;

use App\Core\JWT;
use App\Traits\ResponseTrait;

class AdminMiddleware
{
  use ResponseTrait;
  private $response;

  public function __construct()
  {
    $this->check();
  }
  public function check($allowedRoles = ['admin'])
  {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
      http_response_code(401);
      return $this->errorResponse('Token is required', 401, ["error" => "Unauthorized"]);
      exit;
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);

    try {
      $user = JWT::decode($token);
      $_REQUEST['currentUser'] = $user;

      if (!$user) {
        http_response_code(401);
        return $this->errorResponse('Invalid or expired token', 401, ["error" => "Invalid or expired token"]);
        exit;
      }

      if (!isset($user['role']) || !in_array($user['role'], $allowedRoles)) {
        http_response_code(403);
        return $this->errorResponse('Forbidden. Access denied.', 403, ['error' => 'Forbidden. Access denied.']);
        exit;
      }
      return $user;
    } catch (\Exception $e) {
      return $this->errorResponse($e->getMessage(), 403, ['error' => $e->getMessage()]);
    }
  }
}
