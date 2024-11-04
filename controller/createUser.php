<?php
require_once '../model/UserModel.php';
ob_start();

// Set response headers to return JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Failed to create user.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $model = new UserModel();

        $success = $model->addUser($username, $email, $password);
        if ($success) {
            $response['success'] = true;
            $response['message'] = "User created successfully!";
        } else {
            $response['message'] = "Failed to create user.";
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }

    // Output JSON response
    echo json_encode($response);
    exit();
} else {
    // Set error message if request method is not POST
    $response['message'] = "Invalid request method.";
    ob_end_clean(); // Clean the output buffer to ensure only JSON is sent
    echo json_encode($response);
    exit();
}
?>