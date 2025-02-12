<?php

namespace App\Controllers;



use App\Models\User;
use App\Core\Request;
use App\Traits\ResponseTrait;

class UserController extends Controller
{
  use ResponseTrait;
  private $userModel;
  private $tokenModel;
  private $request;
  private $isApi;
  public $currentUser;

  public function __construct()
  {
    $this->userModel = new User();
    $this->request = new Request();
    $this->isApi = $this->isApi($_SERVER['REQUEST_URI']);

    if ($this->isApi) {
      $this->currentUser =  $this->userModel->find($_REQUEST['currentUser']['id']) ?? null;
      return;
    }
    $this->currentUser = $this->userModel->find($_SESSION['user']) ?? null;
    unset($this->currentUser->password);

    if (!$this->currentUser || !isset($_SESSION['user'])) {
      unset($_SESSION['user'], $_SESSION['role']);
      header('Location: ' . BASE_PATH . '/login');
    }
  }

  public function index()
  {
    $users = $this->userModel->all();

    if ($this->isApi) {
      if (count($users) > 0) {
        foreach ($users as $user) {
          // Ensure that $user->cv has a valid value (path or filename)
          if (!empty($user->cv)) {
            $protocol = $_SERVER['REQUEST_SCHEME'] ?? null;  // Get 'http' or 'https'
            $host = $_SERVER['HTTP_HOST'] ?? null;           // Get host (domain name or IP)
            $basePath = BASE_PATH ?? '';             // Base path (if any, otherwise default to empty)

            // Construct the full URL for the CV file
            $user->cv = $host . $basePath . "/assets/uploads/cvs/" . $user->cv;
          }
        }
      }
      return $this->successResponse('Success', $users);
    }

    if ($this->currentUser->role == 'admin') {
      echo $this->render('/dashboard/index', ['currentUser' => $this->currentUser, 'users' => $users], $meta = "Dashboard - Customer record");
    }

    if ($this->currentUser->role == 'customer') {
      echo $this->render('/dashboard/userindex', ['currentUser' => $this->currentUser, 'user' => $this->currentUser], $meta = "Dashboard - Customer record");
    }
  }

  public function create()
  {
    echo $this->render('/dashboard/create', ['currentUser' => $this->currentUser,], $meta = "Dashboard - Add new customer record");
  }

  public function edit($id)
  {
    $user = $this->userModel->find($id);
    echo $this->render('/dashboard/edit', ['currentUser' => $this->currentUser, 'user' => $user], $meta = "Dashboard - Edit new customer record");
  }
  // Create a new customer record
  public function store()
  {
    $name = $this->request->input('name');
    $email = $this->request->input('email');
    $password = $this->request->input('password');
    $phone = $this->request->input('phone');

    $cvFile = $this->request->getFiles()['cv'] ?? null;
    $cvFileName = null;


    if (empty($name) || empty($email) || empty($password) || empty($phone)) {
      if ($this->isApi) {
        return $this->errorResponse('All fields are required', 400);
      }
      $_SESSION['error'] = 'All fields are required';
      return $this->redirect('dashboard/create');
    }


    if ($cvFile['name'] !== '') {
      $uploadResult = $this->userModel->handleFileUpload($cvFile);
      if (isset($uploadResult['error'])) {
        if ($this->isApi) {
          return $this->errorResponse($uploadResult['error'], 400);
        }
        $_SESSION['error'] = $uploadResult['error'];
        return $this->redirect('dashboard/create');
      }
      $cvFileName = $uploadResult['success'];
    }

    $user = $this->userModel->findByEmail($email);

    if ($user) {
      if ($this->isApi) {
        return $this->errorResponse('User with the email already exists', 401);
      }

      return $this->redirect('dashboard/create', 'User with the email already exists');
    }

    $customer = $this->userModel->create($name, $email, $phone, $password, 'customer', $cvFileName);

    if ($customer) {
      if ($this->isApi) {
        return $this->successResponse('User created successfully', null, 201);
      }
      $_SESSION['success'] = 'User created successfully';
      return $this->redirect('dashboard/create');
    } else {
      if ($this->isApi) {
        return $this->errorResponse('An error occurred while creating the customer', 500);
      }
      $_SESSION['error'] = 'An error occurred while creating the customer';
      return $this->redirect('dashboard/create');
    }
  }


  // Get a customer record by ID
  public function get($id)
  {
    $customer = $this->userModel->find($id);
    if ($customer) {
      return $this->successResponse('Success', $customer);
    } else {
      return $this->errorResponse('User not found', 404, ['error' => 'User not found']);
    }
  }

  public function me()
  {
    return $this->get($this->currentUser->id);
  }

  // Update an existing customer record
  public function update($id)
  {
    $currentUser = $this->currentUser;
    $sessionId = $currentUser->id;

    if ($sessionId !== $id && $currentUser->role !== 'admin') {
      if ($this->isApi) {
        return $this->errorResponse('Unauthorized', 401, ['error' => 'Unauthorized']);
      }
      $_SESSION['error'] = 'Unauthorized';
      return $this->redirect('dashboard/edit/' . $id);
    }

    $name = $this->request->input('name');
    $email = $this->request->input('email');
    $phone = $this->request->input('phone');
    $cvFile = $this->request->getFiles()['cv'] ?? null;
    $cvFileName = null;

    if (empty($name) || empty($email) || empty($phone)) {
      if ($this->isApi) {
        return $this->errorResponse('All fields are required', 400, ['error' => 'All fields are required']);
      }
      $_SESSION['error'] = 'All fields are required';
      return $this->redirect('dashboard/edit/' . $id);
    }

    if (isset($csvFile) && $cvFile['name'] !== '') {
      $uploadResult = $this->userModel->handleFileUpload($cvFile);
      if (isset($uploadResult['error'])) {
        if ($this->isApi) {
          return $this->errorResponse($uploadResult['error'], 400);
        }
        $_SESSION['error'] = $uploadResult['error'];
        return $this->redirect('dashboard/create');
      }
      $cvFileName = $uploadResult['success'];
    }

    $updateData = [
      'name' => $name,
      'email' => $email,
      'phone' => $phone,
    ];
    if ($cvFileName) {
      $updateData['cv'] = $cvFileName;
    }
    // Update the customer record in the database
    $customer = $this->userModel->update($id, $updateData);
    if ($customer) {
      if ($this->isApi) {
        return $this->successResponse('User updated successfully', [], 200);
      }
      $_SESSION['success'] = 'User updated successfully';
      return $this->redirect('dashboard/edit/' . $id);
    } else {
      if ($this->isApi) {
        return $this->errorResponse('An error occurred while updating the customer', 400, ['error' => 'An error occurred while updating the customer']);
      }
      $_SESSION['error'] = 'An error occurred while updating the customer';
      return $this->redirect('dashboard/edit/' . $id);
    }
  }

  // Delete a customer record by ID
  public function delete($id)
  {
    $id = intval($id);
    if ($this->currentUser->role == 'admin') {
      $user = $this->userModel->find($id);

      if (!$user) {
        if ($this->isApi) {
          return $this->errorResponse('User not found', 404);
        }
        $_SESSION['error'] = 'User not found';
        return $this->redirect('dashboard');
      }

      $deleted = $this->userModel->delete($id);
      if ($deleted) {
        if ($this->isApi) {
          return $this->successResponse('User deleted successfully');
        }
        $_SESSION['success'] = 'User deleted successfully';
        return $this->redirect('dashboard');
      } else {
        if ($this->isApi) {
          return $this->errorResponse('An error occurred while deleting the customer', 400, ['error' => 'An error occurred while deleting the customer']);
        }
        $_SESSION['error'] = 'An error occurred while deleting the customer';
        return $this->redirect('dashboard');
      }
    }
  }
}
