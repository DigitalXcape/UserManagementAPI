<?php
require_once '../model/UserModel.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Failed to save story progress'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['userId']) && isset($_POST['storyPage'])) {
        $userId = $_POST['userId'];
        $storyPage = $_POST['storyPage'];

        try {
            $model = new UserModel();
            $result = $model->updateStoryPageById($userId, $storyPage);

            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Story progress saved successfully';
                http_response_code(200);
            } else {
                $response['message'] = 'Story page could not be updated or no changes were made';
                http_response_code(401);
            }
        } catch (Exception $e) {
            $response['message'] = "Error: " . $e->getMessage();
            http_response_code(500);
        }
    } else {
        $response['message'] = 'Invalid data provided';
        http_response_code(401);
    }
} else {
    $response['message'] = 'Invalid request method';
    http_response_code(401);
}

echo json_encode($response);
exit();
?>