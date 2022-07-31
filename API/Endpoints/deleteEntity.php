<?php
/**
 * Deletes an entity
 *
 * Deletes an entity identified by its id from the database.
 * We don't bother deleting activities associated with the entity.
 * We just delete the entity.
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
/** @var string $entityId URI Parameter */

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Delete Type */
$deleteEntityPrepared = $db->prepare("DELETE FROM entities WHERE `id` = ?;");
$deleteEntityPrepared->bind_param("s", $entityId);
$deleteEntitySuccess = $deleteEntityPrepared->execute();

if (!$deleteEntitySuccess) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Could not delete entity."));
    exit();
}

/* Send Response */
echo json_encode(["success" => true, 'message' => 'Entity deleted']);

/* Add Activity Item */
include './API/Helpers/addActivityItem.php';
try {
    addActivityItem(
        $db,
        'entity',
        $entityId,
        ':user :verb device',
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