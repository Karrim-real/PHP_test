<?php

namespace App\Traits;

trait ResponseTrait
{
  /**
   * Return a success JSON response.
   *
   * @param mixed $data
   * @param string $message
   * @param int $statusCode
   * @return void
   */
  public function successResponse($message = 'Success', $data = null, $statusCode = 200, $extra = null)
  {
    http_response_code($statusCode);
    $result = [
      'success' => true,
      'message' => $message,
    ];

    if (isset($data) && !empty($data)) {
      $result['data'] = $data;
    }


    if (isset($extra) && !empty($extra)) {
      $result['extra'] = $extra;
    }

    echo json_encode($result);
    exit; // Ensure no further output is added
  }

  /**
   * Return an error JSON response.
   *
   * @param string $message
   * @param int $statusCode
   * @param mixed $errors
   * @return void
   */
  public function errorResponse($message = 'An error occurred', $statusCode = 400, $errors = null)
  {
    http_response_code($statusCode);
    $result = [
      'success' => false,
      'message' => $message
    ];

    if (isset($errors) && !empty($errors)) {
      $result['errors'] = $errors;
    }

    echo json_encode($result);
    exit; // Ensure no further output is added
  }
}
