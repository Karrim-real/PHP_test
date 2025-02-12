<?php

namespace App\Controllers;

use App\Models\Customer;
use App\Core\Request;

class CustomerController
{
  // Create a new customer record
  public function create()
  {
    $name = Request::get('name');
    $email = Request::get('email');
    $phone = Request::get('phone');
    $cv = Request::getFiles()['cv'] ?? null;

    if (empty($name) || empty($email) || empty($phone)) {
      echo json_encode(['error' => 'All fields are required']);
      return;
    }

    // Save the customer record in the database
    $customer = Customer::create($name, $email, $phone, $cv = null);
    if ($customer) {
      echo json_encode(['message' => 'Customer created successfully']);
    } else {
      echo json_encode(['error' => 'An error occurred while creating the customer']);
    }
  }

  // Get a customer record by ID
  public function get($id)
  {
    $customer = Customer::find($id);
    if ($customer) {
      echo json_encode($customer);
    } else {
      echo json_encode(['error' => 'Customer not found']);
    }
  }

  // Update an existing customer record
  public function update($id)
  {
    $name = Request::get('name');
    $email = Request::get('email');
    $phone = Request::get('phone');
    $cv = Request::getFiles()['cv'] ?? null;

    if (empty($name) || empty($email) || empty($phone)) {
      echo json_encode(['error' => 'All fields are required']);
      return;
    }

    // Update the customer record in the database
    $customer = Customer::update($id, $name, $email, $phone, $cv);
    if ($customer) {
      echo json_encode(['message' => 'Customer updated successfully']);
    } else {
      echo json_encode(['error' => 'An error occurred while updating the customer']);
    }
  }

  // Delete a customer record by ID
  public function delete($id)
  {
    $deleted = Customer::delete($id);
    if ($deleted) {
      echo json_encode(['message' => 'Customer deleted successfully']);
    } else {
      echo json_encode(['error' => 'An error occurred while deleting the customer']);
    }
  }
}
