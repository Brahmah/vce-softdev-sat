<?php
/**
 * Create a new user.
 *
 * These users are used to login to the system. They are also used to manage
 * the settings of the system. Users have a role that determines what they can
 * and cannot do.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
/* Imports */
require_once './API/Helpers/initDatabaseConnection.php';

/* Database */
$db = getConnection();

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Validate */
$requiredFields = ['username', 'password', 'role'];
foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || $_POST[$field] == "") {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Missing required field: " . $field));
        exit();
    }
}
$possibleRoles = ['standard', 'superuser'];
if (!in_array($_POST['role'], $possibleRoles)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Invalid role: " . $_POST['role'] . ". Valid roles are: " . implode(', ', $possibleRoles)));
    exit();
}

/* "Make password super-duper obfuscated, secure and sh*t" --MC */
$hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT, ['cost' => 12]);

/* Add User */
$createUserPrepared = $db->prepare("INSERT INTO users (`username`, `password`, `role`, `created_timestamp`, `created_by_user_id`) VALUES (?, ?, ?, ?, ?);");
$timestamp = time();
$createUserPrepared->bind_param("sssii", $_POST['username'], $hashedPassword, $_POST['role'], $timestamp, $_SESSION['user_id']);
$createUserSuccess = $createUserPrepared->execute();
$newUserId = $db->insert_id;

if (!$createUserSuccess) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Could not add user."));
    exit();
}

/* Send Response */
echo json_encode(["success" => true, 'message' => 'A new user is born.']);

/* Add Activity Item */
include './API/Helpers/addActivityItem.php';
try {
    addActivityItem(
        $db,
        'user',
        $newUserId,
        ':enactor created user :newUser',
        [
            'enactor' => [
                'text' => $_SESSION['username'],
                'href' => '/SAT_BRH/settings'
            ],
            'newUser' => [
                'text' => $_POST['username'],
                'href' => '/SAT_BRH/settings'
            ]
        ]
    );
} catch (Exception $e) {
    // Do nothing, bad luck
}