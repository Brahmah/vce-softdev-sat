<?php

// Send back 500 error as json
header('Content-Type: application/json');
header('HTTP/1.1 500 Internal Server Error');
$response = [
    'status' => 'error',
    'success' => false,
    'message' => 'Internal Server Error'
];
echo json_encode($response);