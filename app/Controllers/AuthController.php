<?php

namespace App\Controllers;

use App\Core\JWT;
use App\Models\User;
use App\Core\Request;
use App\Traits\ResponseTrait;

class AuthController extends Controller
{
  use ResponseTrait;
  private $userModel;
  private $tokenModel;
  private $request;
  private $isapi;

  public function __construct()
  {
    $this->userModel = new User();
    $this->request = new Request();
    $this->isapi = $this->isApi($_SERVER['REQUEST_URI']);
  }

  public function showLogin()
  {
    $user = $_SESSION['user'] ?? null;
    if ($user) {
      return $this->redirect('dashboard');
    }
    echo $this->render('auth/login');
  }

  public function showRegister()
  {
    echo $this->render('auth/register');
  }

  public function login()
  {
    $data = $this->request->getBody();
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    if (!$email || !$password) {

      if ($this->isapi) {
        return $this->errorResponse('Email and password are required', 400);
      }

      return $this->redirect('login', 'Email and password are required');
    }

    $user = $this->userModel->findByEmail($email);

    if (!$user || !$this->userModel->verifyPassword($password, $user->password)) {
      if ($this->isapi) {
        return $this->errorResponse('Invalid credentials', 401);
      }

      return $this->redirect('login', 'Invalid credentials');
    }


    if ($this->isapi) {
      $token = JWT::encode(["id" => $user->id, "email" => $user->email, "role" => $user->role]);
      $data = ['token' => $token];
      return $this->successResponse('Login successful', $data, 200);
    }
    $_SESSION['user'] = $user->id;
    $_SESSION['role'] = $user->role;
    return $this->redirect('dashboard');
  }

  public function register()
  {
    $data = $this->request->getBody();

    $name = $data['name'] ?? null;
    $email = $data['email'] ?? null;
    $phone = $data['phone'] ?? null;
    $password = $data['password'] ?? null;



    if (!$email || !$password || !$phone || !$name) {
      if ($this->isapi) {
        return $this->errorResponse('All fields are required', 400);
      }

      return $this->redirect('register', 'All fields are required');
    }

    $user = $this->userModel->findByEmail($email);

    if ($user) {
      if ($this->isapi) {
        return $this->errorResponse('User with the email already exists', 401);
      }

      return $this->redirect('register', 'User with the email already exists');
    }
    $result = $this->userModel->create($name, $email, $phone, $password);
    if (!$result) {
      if ($this->isapi) {
        return $this->errorResponse('An error occurred while creating the user', 500);
      }
      return $this->redirect('register', 'An error occurred while creating the user');
    }
    if ($result) {
      if ($this->isapi) {
        return $this->successResponse('User created successfully', null, 200);
      }
      return $this->redirect('login', null, 'User created successfully, Please login');
    }
  }

  public function logout()
  {

    if ($this->isapi) {

      $headers = getallheaders();

      if (!isset($headers['Authorization'])) {
        return $this->errorResponse('Authorization header missing', 401);
      }

      if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        $token = $matches[1];
        $this->tokenModel->delete($token);
        return $this->successResponse('Logged out successfully', null, 200);
      } else {
        return $this->errorResponse('Invalid Authorization format', 400);
      }
    }

    unset($_SESSION['user']);
    unset($_SESSION['role']);
    return $this->redirect('login');
  }
}
