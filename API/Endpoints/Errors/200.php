<?php
// this isn't really and error endpoint.
// Send back 200 status as json
header('Content-Type: application/json');
header('HTTP/1.1 200 OK');
$response = [
    'status' => 'success',
    'success' => true,
    'message' => 'You are authenticated.'
];
echo json_encode($response);