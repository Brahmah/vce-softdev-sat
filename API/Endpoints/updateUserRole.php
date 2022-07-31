<?php
/**
 * Update a user's role.
 *
 * This endpoint is used to promote or demote a user.
 * There are some restrictions on who can promote or demote a user.
 * This endpoint will only promote or demote a user if:
 * - the enactor has a superuser role.
 * - the enactor is not the same as the user.
 * - the enactor is not the same as the user's creator.
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
    echo json_encode(array("success" => false, "message" => "You cannot update your own role :)"));
    exit();
}
if (!isset($_POST['newRole'])) { // if newRole is not set
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Missing new role."));
    exit();
}
$possibleRoles = array("superuser", "standard");
if (!in_array($_POST['newRole'], $possibleRoles)) { // if newRole is not valid
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Invalid new role. Must be one of: " . implode(", ", $possibleRoles)));
    exit();
}

/* Update user role */
$updateUserRolePrepared = $db->prepare("UPDATE users SET `role` = ? WHERE `id` = ?;");
$updateUserRolePrepared->bind_param("si", $_POST['newRole'], $userId);
$updateUserRoleSuccess = $updateUserRolePrepared->execute();

if (!$updateUserRoleSuccess) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Could not update user role."));
    exit();
}

/* Send Response */
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Updated role.']);

/* Get user */
$getUserPrepared = $db->prepare("SELECT * FROM users WHERE `id` = ?;");
$getUserPrepared->bind_param("i", $userId);
$getUserPrepared->execute();
$getUser = $getUserPrepared->get_result()->fetch_assoc();

/* Add Activity Item */
include './API/Helpers/addActivityItem.php';
try {
    addActivityItem(
        $db,
        'user',
        $userId,
        ':enactor updated the role of user :target to :newRole',
        [
            'enactor' => [
                'text' => $_SESSION['username'],
                'href' => '/SAT_BRH/settings'
            ],
            'target' => [
                'text' => $getUser['username'],
                'href' => '/SAT_BRH/settings'
            ],
            'newRole' => [
                'text' => $_POST['newRole'],
                'color' => $_POST['newRole'] === 'superuser' ? '#ff5722' : '#673ab7'
            ]
        ]
    );
} catch (Exception $e) {
    // Do nothing, bad luck
}