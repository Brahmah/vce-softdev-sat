<?php
/**
 * Deletes an entity type
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
/** @var string $typeId URI Parameter */

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Delete Type */
$deleteEntityTypePrepared = $db->prepare("DELETE FROM entity_types WHERE `entity_type_id` = ?;");
$deleteEntityTypePrepared->bind_param("i", $typeId);
$deleteEntityTypeSuccess = $deleteEntityTypePrepared->execute();

if (!$deleteEntityTypeSuccess) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Could not delete entity type."));
    exit();
}

/* Send Response */
echo json_encode(["success" => true, 'message' => 'Type deleted']);

/* Add Activity Item */
include './API/Helpers/addActivityItem.php';
try {
    addActivityItem(
        $db,
        'user',
        $_SESSION['user_id'],
        ':user :verb an entity type',
        [
            'user' => [
                'text' => $_SESSION['username'],
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