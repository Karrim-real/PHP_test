<?php


use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\CustomerController;
use App\Controllers\UserController;
use App\Middleware\AdminMiddleware;

// Initialize router
$router = new Router();
//Define web routes
$router->get('', [AuthController::class, 'showLogin']);
$router->post('', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [AuthController::class, 'register']);


//Authenticated routes
$router->get('/dashboard', [UserController::class, 'index']);
$router->get('/dashboard/create', [UserController::class, 'create']);
$router->post('/dashboard/create', [UserController::class, 'store']);
$router->get('/dashboard/edit/{id}', [UserController::class, 'edit']);
$router->post('/dashboard/edit/{id}', [UserController::class, 'update']);
$router->post('/dashboard/delete/{id}', [UserController::class, 'delete']);
$router->get('/logout', [AuthController::class, 'logout']);

// Define api routes
$router->post('/api/login', [AuthController::class, 'login']);
//$router->post('/api/logout', [AuthController::class, 'logout'], [AdminMiddleware::class]);


//Authenticated routes (Needs Bearer Token)
//Get current logged in user
$router->get("/api/me", [UserController::class, 'me'], [AdminMiddleware::class]);

//Get All Customers
$router->get('/api/customers', [UserController::class, 'index'], [AdminMiddleware::class]);
// Create a new customer
$router->post('/api/customers', [UserController::class, 'store'], [AdminMiddleware::class]);

// Get a customer by ID
$router->get('/api/customers/{id}', [UserController::class, 'get'], [AdminMiddleware::class]);

// Update a customer by ID
$router->post('/api/customers/edit/{id}', [UserController::class, 'update'], [AdminMiddleware::class]);

// Delete a customer by ID
$router->post('/api/customers/delete/{id}', [UserController::class, 'delete'], [AdminMiddleware::class]);

// Resolve the request
$router->resolve($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
