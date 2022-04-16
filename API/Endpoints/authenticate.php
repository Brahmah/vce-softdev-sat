<?php

$credentials = [
    'username' => $_POST['username'],
    'password' => $_POST['password']
];

if ($credentials['username'] == 'admin' && $credentials['password'] == 'admin') {
    // begin session
    session_start();
    $_SESSION['authenticated'] = true;
    $_SESSION['username'] = $credentials['username'];
    // tell client that they are authenticated
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} else {
    // send error to client
    header('Content-Type: application/json');
    echo json_encode(['success' => false]);
}