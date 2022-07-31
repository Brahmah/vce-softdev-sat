<?php
/**
 * Authenticates the user and sets the session.
 *
 * Loads the user from the database and sets the session.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */

/* Imports */
require_once './API/Helpers/initDatabaseConnection.php';

/* Database */
$db = getConnection();

/* Validate */
$requiredFields = ['username', 'password'];
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field])) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Missing required field: " . $field));
        exit();
    }
}

/* Credentials */
$credentials = [
    'username' => $_POST['username'],
    'password' => $_POST['password']
];

if (!$credentials['username'] || !$credentials['password']) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "username or password is empty"));
    exit();
}

/* Get User */
$getUserPrepared = $db->prepare("SELECT * FROM users WHERE username = ?;");
$getUserPrepared->bind_param("s", $credentials['username']);
$getUserPrepared->execute();
$getUserResult = $getUserPrepared->get_result();
$rawDbUser = $getUserResult->fetch_assoc();

if ($rawDbUser == null) {
    http_response_code(401);
    echo json_encode(array("success" => false, "message" => "Invalid username or password."));
    exit();
}

/* Check Password */
if (!password_verify($credentials['password'], $rawDbUser['password'])) {
    http_response_code(401);
    echo json_encode(array("success" => false, "message" => "Invalid username or password."));
    exit();
}

/* Setup Session */
$_SESSION['authenticated'] = true;
$_SESSION['username'] = $credentials['username'];
$_SESSION['role'] = $rawDbUser['role'];
$_SESSION['user_id'] = $rawDbUser['id'];
$_SESSION['login_timestamp'] = time();


/* Send Response */
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'You are now authenticated.']);

/* Add Activity Item */
include './API/Helpers/addActivityItem.php';
try {
    addActivityItem(
        $db,
        'user',
        $_SESSION['user_id'],
        ':user logged in',
        [
            'user' => [
                'text' => $_SESSION['username'],
                'href' => '/SAT_BRH/settings'
            ]
        ]
    );
} catch (Exception $e) {
    // Do nothing, bad luck
}