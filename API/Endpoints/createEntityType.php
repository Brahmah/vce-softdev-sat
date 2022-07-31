<?php
/**
 * Create a new entity type.
 *
 * Entity types are used to define the type of entity that is being created.
 * This provisions the customization of settings and editable fields for each
 * entity type.
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

/* Validation */
if (!isset($_POST['label'])) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Missing label parameter."));
    die();
}
if ($_POST['label'] == "") {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Label cannot be empty."));
    die();
}

/* Create Entity Type */
$createEntityTypePrepared = $db->prepare("INSERT INTO entity_types (`type_label`, `type_created_timestamp`) VALUES (?, ?);");
$timestamp = time();
$createEntityTypePrepared->bind_param("si", $_POST['label'], $timestamp);
$createEntityTypeSuccess = $createEntityTypePrepared->execute();
$newEntityTypeId = $db->insert_id;

if (!$createEntityTypeSuccess) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Could not create entity type."));
    exit();
}

/* Send Response */
echo json_encode(["success" => true, 'message' => 'Entity Type Added']);

/* Add Activity Item */
include './API/Helpers/addActivityItem.php';
try {
    addActivityItem(
        $db,
        'user',
        $_SESSION['user_id'],
        ':user created new entity type :newEntityType',
        [
            'user' => [
                'text' => $_SESSION['username'],
                'href' => '/SAT_BRH/settings'
            ],
            'newEntityType' => [
                'text' => $_POST['label'],
                'href' => '/SAT_BRH/settings/device_types/' . $newEntityTypeId
            ]
        ]
    );
} catch (Exception $e) {
    // Do nothing, bad luck
}