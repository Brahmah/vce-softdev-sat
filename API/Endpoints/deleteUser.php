<?php
/**
 * Deletes a user
 *
 * Also adds an activity log entry
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
/* Imports */
require_once './API/Helpers/initDatabaseConnection.php';

/* Database */
$db = getConnection();

/* URI Parameters */
/** @var int $userId URI Parameter */

/* Validation */
if (!isset($userId)) { // If userId is not set
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Missing user id."));
    exit();
}
if (!is_numeric($userId)) { // If userId is not numeric
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "User id must be numeric."));
    exit();
}
if ($userId == $_SESSION['user_id']) { // if user id is not current user id
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "You cannot delete yourself :)"));
    exit();
}

/* Get user */
$getUserPrepared = $db->prepare("SELECT * FROM users WHERE `id` = ?;");
$getUserPrepared->bind_param("i", $userId);
$getUserPrepared->execute();
$getUser = $getUserPrepared->get_result()->fetch_assoc();

/* Delete User */
$deleteUserPrepared = $db->prepare("DELETE FROM users WHERE `id` = ?;");
$deleteUserPrepared->bind_param("i", $userId);
$deleteUserSuccess = $deleteUserPrepared->execute();

if (!$deleteUserSuccess) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Could not delete user."));
    exit();
}

/* Send Response */
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'User deleted']);

/* Add Activity Item */
include './API/Helpers/addActivityItem.php';
try {
    addActivityItem(
        $db,
        'user',
        $userId,
        ':enactor :verb the user :deletedUser',
        [
            'enactor' => [
                'text' => $_SESSION['username'],
                'href' => '/SAT_BRH/settings'
            ],
            'deletedUser' => [
                'text' => $getUser['username'],
                'href' => '/SAT_BRH/settings'
            ],
            'verb' => [
                'text' => 'deleted',
                'color' => 'red'
            ]
        ]
    );
} catch (Exception $e) {
    // Do nothing, bad luck
}