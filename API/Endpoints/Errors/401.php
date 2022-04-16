<?php
// Send back 401 error as json
header('Content-Type: application/json');
header('HTTP/1.1 401 Unauthorized');
$response = [
    'status' => 'error',
    'message' => 'Unauthorized you fool!'
];
echo json_encode($response);