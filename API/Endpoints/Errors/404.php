<?php
// Send back 404 error as json
header('Content-Type: application/json');
header('HTTP/1.1 404 Not Found');
$response = [
    'status' => 'error',
    'success' => false,
    'message' => 'Not Found'
];
echo json_encode($response);