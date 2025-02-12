<?php

namespace App\Core;

use PDO;
use App\Traits\ResponseTrait;

class Database
{
  use ResponseTrait;
  private static $connection = null;

  public static function connect()
  {
    if (self::$connection === null) {
      $env = parse_ini_file(__DIR__ . '/../../.env');
      $host = $env['DB_HOST'] ?? 'localhost';
      $name = $env['DB_NAME'] ?? '';
      $user = $env['DB_USER'] ?? '';
      $pass = $env['DB_PASSWORD'] ?? '';

      // Ensure the database exists before connecting
      self::createDatabaseIfNotExists($host, $name, $user, $pass);

      $dsn = "mysql:host={$host};dbname={$name}";

      try {
        self::$connection = new PDO($dsn, $user, $pass);
        self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (\PDOException $e) {
        return (new self)->errorResponse("Database connection failed: " . $e->getMessage());
        exit;
      }
    }
    return self::$connection;
  }

  private static function createDatabaseIfNotExists($host, $dbName, $user, $pass)
  {
    try {
      // Connect to MySQL without specifying a database
      $dsn = "mysql:host={$host}";
      $pdo = new PDO($dsn, $user, $pass);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Create the database if it doesn't exist
      $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
    } catch (\PDOException $e) {
      return (new self)->errorResponse("Failed to create database: " . $e->getMessage());
      exit;
    }
  }
}
