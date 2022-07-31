<?php
// Send back 403 error as json
header('Content-Type: application/json');
header('HTTP/1.1 403 Forbidden');
$response = [
    'status' => 'error',
    'success' => false,
    'message' => 'Forbidden'
];
echo json_encode($response);