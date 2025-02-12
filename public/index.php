<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
//ini_set('session.save_path', realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/./sessions'));
session_start();  // Start the session
require_once __DIR__ . '/../config/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/cors.php';


// Load routes
require_once __DIR__ . '/../config/routes.php';
