<?php
require_once '../model/UserModel.php';
require_once '../utils/jwtUtils.php'; // Ensure you have the validateJWT function in a utils file or include it here

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Failed to fetch story page',
    'page' => null
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';

    // Validate the JWT token
    $payload = validateJWT($token);
    if ($payload === null) {
        $response['message'] = 'Invalid or expired token';
        echo json_encode($response);
        exit();
    }

    // Token is valid; retrieve user ID from payload
    $userId = $payload['user_id'];

    try {
        $model = new UserModel();
        $pageString = $model->getStoryPageById($userId);

        if ($pageString) {
            $response['success'] = true;
            $response['page'] = $pageString;
            $response['message'] = 'Story page retrieved successfully';
            http_response_code(200);
        } else {
            $response['message'] = 'Story page not found for user';
            http_response_code(401);
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
        http_response_code(500);
    }
} else {
    $response['message'] = 'Invalid request method';
    http_response_code(401);
}

echo json_encode($response);
exit();
?>