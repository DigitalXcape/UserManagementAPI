<?php
require_once '../model/UserModel.php';
require_once '../logger/Logger.php';

header('Content-Type: application/json');

$logger = Logger::getInstance();

$response = [
    'success' => false,
    'users' => [],
    'message' => 'Failed to retrieve users'
];

try {
    $model = new UserModel();

    //get the data of all the users from the usermodel
    $users = $model->getData();

    //if data is found from the model, send the response accordingly
    if (!empty($users)) {
        $response['success'] = true;
        $response['users'] = $users;
        $response['message'] = 'Users retrieved successfully';
        $logger->log("Users retrieved: " . count($users) . " users found.");
        http_response_code(200);
    } 
    else {
        //no data was found, log it
        $response['message'] = 'No users found';
        $logger->log("No users found in the database.");
    }
} catch (Exception $e) {
    $response['message'] = "Error: " . $e->getMessage();
    $logger->log("Exception occurred: " . $e->getMessage());
    http_response_code(500);
}

echo json_encode($response);
exit();