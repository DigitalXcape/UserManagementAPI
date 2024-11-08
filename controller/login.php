<?php
require_once '../classes/user.php';
require_once '../logger/Logger.php';
require_once '../model/UserModel.php';

define('JWT_SECRET_KEY', '435dgh47n349sn783hf839db3');

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Invalid email or password',
    'token' => null
];

// Function to create a JWT
function generateJWT($header, $payload, $secret = JWT_SECRET_KEY) {
    $base64UrlHeader = base64UrlEncode(json_encode($header));
    $base64UrlPayload = base64UrlEncode(json_encode($payload));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = base64UrlEncode($signature);
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function base64UrlEncode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logger = Logger::getInstance();

    // Get the email and password sent to the API
    $email = $_POST['email'];
    $password = $_POST['password'];
    $logger->log("Attempting Login with email: $email");

    try {
        $model = new UserModel();
        $user = $model->getUserByEmail($email);

        if ($user && $model->validateUser($email, $password)) {
            // Set the story page for the user
            $user->setStoryPage($model->getStoryPageById($user->getId()));

            // Create the JWT header and payload
            $header = ['alg' => 'HS256', 'typ' => 'JWT'];

            $payload = [
                'user_id' => $user->getId(),
                'username' => $user->getUserName(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
                'story_page' => $user->getStoryPage(),
                'exp' => time() + 3600 
            ];

            $jwt = generateJWT($header, $payload);

            // Set the API response variables
            $response['success'] = true;
            $response['message'] = 'Login successful';
            $response['token'] = $jwt;
            $response['username'] = $user->getUserName();
            $response['user_id'] = $user->getId();
            $response['role'] = $user->getRole();
            http_response_code(200); // OK

        } else {
            $logger->log("Invalid email or password");
            http_response_code(401); // Unauthorized
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
        http_response_code(500); // Internal Server Error
    }
}
echo json_encode($response);
exit();
?>