<?php
require_once '../model/UserModel.php';

// Set response headers to return JSON
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'User deletion failed'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'];

    try {
        $model = new UserModel();
        
        // Attempt to delete the user
        $success = $model->deleteUserById($userId);

        if ($success) {
            $response['success'] = true;
            $response['message'] = 'User deleted successfully';
        } else {
            $response['message'] = 'Failed to delete user';
        }
    } catch (Exception $e) {
        $response['message'] = "Error: " . $e->getMessage();
    }
}

// Output JSON response
echo json_encode($response);
exit();
?>