<?php
require_once '../classes/user.php';
require_once '../logger/Logger.php';
require_once '../model/UserModel.php';

// Define a secret key for encoding/decoding the JWT
define('JWT_SECRET_KEY', 'your_secret_key_here');

// Set response headers to return JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Invalid email or password',
    'token' => null
];

/**
 * Function to generate a JWT token
 */
function generateJWT($header, $payload, $secret = JWT_SECRET_KEY) {
    $base64UrlHeader = base64UrlEncode(json_encode($header));
    $base64UrlPayload = base64UrlEncode(json_encode($payload));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = base64UrlEncode($signature);
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

/**
 * Helper function to encode in Base64 URL format
 */
function base64UrlEncode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logger = Logger::getInstance();

    $email = $_POST['email'];
    $password = $_POST['password'];
    $logger->log("Attempting Login with email: $email");

    try {
        $model = new UserModel();
        $user = $model->getUserByEmail($email);

        if ($user && $password === $user->getPassword()) {
            // Define the JWT header and payload
            $header = ['alg' => 'HS256', 'typ' => 'JWT'];
            $payload = [
                'user_id' => $user->getId(),
                'username' => $user->getUserName(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
                'exp' => time() + 3600 
            ];
        
            // Generate the JWT
            $jwt = generateJWT($header, $payload);
        
            // Return the JWT in the response
            $response['success'] = true;
            $response['message'] = 'Login successful';
            $response['token'] = $jwt;
            $response['username'] = $user->getUserName();
            $response['user_id'] = $user->getId();
            $response['role'] = $user->getRole();

        } else {
            // Log the invalid login attempt
            $logger->log("Invalid email or password");
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }
}

// Output JSON response with the JWT token if successful
echo json_encode($response);
exit();
?>