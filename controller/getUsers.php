<?php
require_once '../model/UserModel.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'users' => [],
    'message' => 'Failed to retrieve users'
];

try {
    $model = new UserModel();
    $users = $model->getAllUsers();

    if (!empty($users)) {
        $response['success'] = true;
        $response['users'] = $users;
        $response['message'] = 'Users retrieved successfully';
    } else {
        $response['message'] = 'No users found';
    }
} catch (Exception $e) {
    $response['message'] = "Error: " . $e->getMessage();
}

echo json_encode($response);
exit();
?>