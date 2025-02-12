<?php
// List of allowed origins
$allowedOrigins = [
  'http://localhost:3000', // React app on port 3000
  'http://localhost:4000', // Another React app or frontend on port 4000
  'http://localhost:5173', // Another React app or frontend on port 5173
  // Add other domains as needed
];

// Check if the incoming request's Origin is in the allowed list
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
  header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
} else {
  // Optionally, you can set a fallback for disallowed origins
  header('Access-Control-Allow-Origin: http://localhost:3000'); // Default origin
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request (for non-simple requests)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  http_response_code(200);
  exit();
}
