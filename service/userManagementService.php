<?php
require_once '../model/UserModel.php';
require_once '../logger/Logger.php';

// Create an instance of the UserModel
$userModel = new UserModel();
$logger = Logger::getInstance();

// Set the content type to JSON
header('Content-Type: application/json');

// Handle the incoming request
$requestMethod = $_SERVER['REQUEST_METHOD'];

define('JWT_SECRET_KEY', '3457345734573457345');

switch ($requestMethod) {
    case 'GET':
        $userId = $_GET['user_id'] ?? null;
        if ($userId) {
            // Fetch a specific user
            $user = $userModel->getUserById($userId);
            if ($user) {
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }
        } else {
            // Fetch all users
            $users = $userModel->getAllUsers();
            echo json_encode(['success' => true, 'users' => $users]);
        }
        break;

    case 'POST':
        $inputData = json_decode(file_get_contents("php://input"), true);
        
        if (isset($inputData['email'], $inputData['password'])) {
            $email = $inputData['email'];
            $password = $inputData['password'];
            
            // Validate user credentials
            $user = $userModel->validateUser($email, $password);
            if ($user) {
                // Prepare JWT header and payload
                $header = [
                    'alg' => 'HS256',
                    'typ' => 'JWT'
                ];
                $payload = [
                    'user_id' => $user['UserID'],
                    'username' => $user['UserName'],
                    'role' => $user['Role'],
                    'exp' => time() + (60 * 60) // Token expiration time (1 hour)
                ];
                
                // Generate JWT
                $token = generateJWT($header, $payload);
                
                echo json_encode([
                    'success' => true,
                    'token' => $token,
                    'username' => $user['UserName'],
                    'user_id' => $user['UserID'],
                    'role' => $user['Role'],
                ]);
            } else {
                // Log the failed attempt
                $logger->log("Failed login attempt for email: $email");
                echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Invalid input']);
        }
        break;

    case 'PUT':
        // Handle updating an existing user
        $inputData = json_decode(file_get_contents("php://input"), true);
        if (isset($inputData['user_id'], $inputData['username'], $inputData['email'])) {
            $userId = $inputData['user_id'];
            $username = $inputData['username'];
            $email = $inputData['email'];

            // Update user details
            $result = $userModel->updateUser($userId, $username, $email);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } else {
                http_response_code(400); // Bad Request
                echo json_encode(['success' => false, 'message' => 'User update failed']);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Invalid input']);
        }
        break;

    case 'DELETE':
        // Handle deleting a user
        $userId = $_GET['user_id'] ?? null; // Expect user_id to be passed as a query parameter
        if ($userId) {
            $result = $userModel->deleteUser($userId);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(['message' => 'Invalid input']);
        }
        break;

    case 'getUsers':
        $users = $this->getAllUsers(); // Assuming this method exists in your UserModel
        echo json_encode($users);
        break;

    default:
        // Handle unsupported request methods
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

function generateJWT($header, $payload, $secret = JWT_SECRET_KEY) {
    $base64UrlHeader = base64UrlEncode(json_encode($header));
    $base64UrlPayload = base64UrlEncode(json_encode($payload));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = base64UrlEncode($signature);
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

?>