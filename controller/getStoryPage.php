<?php
require_once '../model/UserModel.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Failed to fetch story page',
    'page' => null
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['userId'])) {
        $userId = $_POST['userId'];

        try {
            $model = new UserModel();
            $pageString = $model->getStoryPageById($userId);

            if ($pageString) {
                $response['success'] = true;
                $response['page'] = $pageString;
                $response['message'] = 'Story page retrieved successfully';
            } else {
                $response['message'] = 'Story page not found for user';
            }
        } catch (Exception $e) {
            $response['message'] = "Error: " . $e->getMessage();
        }
    } else {
        $response['message'] = 'User ID not provided';
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
exit();
?>