<?php
/**
 * Gets the current user's session.
 *
 * Will also return the user's roles and permissions.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Send Response */
$me = [
    'success' => true,
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    'role' => $_SESSION['role'],
    'csrf_token' => $_SESSION['csrf'],
    'last_login' => [
        'timestamp' => $_SESSION['login_timestamp'],
        'date' => date('Y-m-d H:i:s', $_SESSION['login_timestamp']),
        'friendly' => date('D j M o, g:i a', (int)$_SESSION['login_timestamp']),
    ],
];

echo json_encode($me);