<?php
/**
 * Reset a user's password.
 *
 * This endpoint will reset a user's password.
 * There are some restrictions on who can reset a user's password.
 * This endpoint will only reset a user's password
 * - if the re-setter has a superuser role.
 * - if the re-setter is not the same as the user.
 * - if the re-setter is not the same as the user's creator.
 * - if the new password is between 8 and 64 characters.
 * - if the new password has more than one non-astric character.
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
    echo json_encode(array("success" => false, "message" => "You cannot reset your own password :)"));
    exit();
}
if (!isset($_POST['newPassword'])) { // if password is not set
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Missing new password."));
    exit();
}
if (strlen($_POST['newPassword']) < 8) { // if password is not long enough
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Password must be at least 8 characters long."));
    exit();
}
if (strlen($_POST['newPassword']) > 64) { // if password is too long
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Password must be at most 64 characters long."));
    exit();
}
if (str_replace('*', '', $_POST['newPassword']) == '') { // if all characters are astrixs, it is not a password
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Password must contain at least one non-astrix character."));
    exit();
}

/* Reset User Password */
$resetUserPasswordPrepared = $db->prepare("UPDATE users SET `password` = ? WHERE `id` = ?;");
$passwordHash = password_hash($_POST['newPassword'], PASSWORD_DEFAULT, ['cost' => 12]);
$resetUserPasswordPrepared->bind_param("si", $passwordHash, $userId);
$resetUserPasswordSuccess = $resetUserPasswordPrepared->execute();

if (!$resetUserPasswordSuccess) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Could not reset user password."));
    exit();
}

/* Send Response */
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Password reset']);

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
        ':enactor reset the password of user :target',
        [
            'enactor' => [
                'text' => $_SESSION['username'],
                'href' => '/SAT_BRH/settings'
            ],
            'target' => [
                'text' => $getUser['username'],
                'href' => '/SAT_BRH/settings'
            ],
        ]
    );
} catch (Exception $e) {
    // Do nothing, bad luck
}