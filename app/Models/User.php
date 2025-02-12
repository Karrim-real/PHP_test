<?php

namespace App\Models;

use App\Core\Database;

class User
{
  private $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  public function find($id)
  {
    $query = $this->db->prepare("SELECT id,name, email,phone, cv,role FROM users WHERE id = :id");
    $query->execute(['id' => $id]);
    return $query->fetch(\PDO::FETCH_OBJ);
  }



  public function all($search = null, $role = 'customer')
  {
    $stmt = $this->db->prepare("SELECT id,name, email,phone, cv FROM users WHERE role = :role");
    if ($search && $search !== '') {
      $stmt = $this->db->prepare("SELECT id,name, email,phone, cv FROM users WHERE role = :role AND name LIKE :search OR email LIKE :search");
      $stmt->bindValue(':search', '%' . $search . '%', \PDO::PARAM_STR);
    }
    $stmt->bindValue(':role', $role, \PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }


  public function findByEmail($email)
  {
    $query = $this->db->prepare("SELECT id,name, email,phone, cv,password,role FROM users WHERE email = :email");
    $query->execute(['email' => $email]);
    return $query->fetch(\PDO::FETCH_OBJ);
  }

  public function verifyPassword($inputPassword, $hashedPassword)
  {
    return password_verify($inputPassword, $hashedPassword);
  }

  public function create($name, $email, $phone, $password, $role = 'customer', $cv = null)
  {
    $query = $this->db->prepare("INSERT INTO users (name, email,phone, password, role, cv) VALUES (:name, :email,:phone, :password, :role, :cv)");
    $query->execute(['name' => $name, 'email' => $email, 'phone' => $phone, 'password' => password_hash($password, PASSWORD_DEFAULT), 'role' => $role, 'cv' => $cv]);
    return $this->db->lastInsertId();
  }

  // public function update($id, $name, $email, $phone, $cv = null)
  // {
  //   $query = $this->db->prepare("UPDATE users SET name = :name, email = :email, phone = :phone, cv = :cv WHERE id = :id");
  //   $query->execute(['name' => $name, 'email' => $email, 'phone' => $phone, 'cv' => $cv, 'id' => $id]);
  //   return $query->rowCount();
  // }

  public function update($id, array $data, $table = 'users')
  {
    if (empty($data)) {
      return false; // No fields to update
    }

    // Dynamically build the SET part of the SQL query
    $fields = [];
    foreach ($data as $key => $value) {
      $fields[] = "$key = :$key";
    }
    $fields = implode(', ', $fields);

    $sql = "UPDATE $table SET $fields WHERE id = :id";

    $stmt = $this->db->prepare($sql);

    // Bind values
    foreach ($data as $key => $value) {
      $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

    return $stmt->execute();
  }

  public function handleFileUpload($file)
  {
    $uploadDir = __DIR__ . "/../../public/assets/uploads/cvs/";

    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }

    $fileTmpLoc = $file["tmp_name"];
    $fileName = preg_replace('#[^a-zA-Z0-9_.-]#', '', $file["name"]);
    $fileSize = $file["size"];
    $fileErrorMsg = $file["error"];
    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    if (!$fileTmpLoc) {
      return ['error' => 'Please select a file before uploading.'];
    }

    if (!in_array($file["type"], $allowedTypes)) {
      return ['error' => 'Only PDF and Word documents (DOC, DOCX) are allowed.'];
    }

    if ($fileSize > 5242880) { // 5MB
      return ['error' => 'Your file exceeds the 5MB limit.'];
    }

    if ($fileErrorMsg) {
      return ['error' => 'An error occurred. Please try again.'];
    }

    // Generate unique filename
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = time() . "_" . uniqid() . "." . $fileExt;

    // Move uploaded file
    if (move_uploaded_file($fileTmpLoc, $uploadDir . $newFileName)) {
      return ['success' => $newFileName];
    } else {
      return ['error' => 'File upload failed.'];
    }
  }


  public function delete($id)
  {
    $query = $this->db->prepare("DELETE FROM users WHERE id = :id");
    $query->execute(['id' => $id]);
    return $query->rowCount();
  }
}
