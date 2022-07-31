<?php
/**
 * Perform operations on an entity field definition.
 *
 * ---- action: ---- determines what operation to performed.
 * - new: -- will add the entity field definition to the database.
 * - update: -- will update the entity field definition in the database.
 * - delete: -- will delete the entity field definition from the database.
 *
 * if the action is null or invalid, the endpoint will return an error.
 *
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

/* Validate Action */
if ($_POST['action'] === null) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Missing action parameter"));
    die();
}

/* Switch Action */
if ($_POST['action'] === 'UPDATE') {
    /* Validate Parameters */
    if ($_POST['newValue'] === null) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Missing newValue parameter"));
        die();
    }
    if ($_POST['field'] == null) { // intentionally not using ===
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Missing description parameter"));
        die();
    }
    $allowedFields = ['label', 'placeholder', 'section', 'type', 'max_length'];
    if (!in_array($_POST['field'], $allowedFields)) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Invalid field parameter. Allowed fields: " . implode(', ', $allowedFields)));
        die();
    }
    /* Save Field */
    $updateFieldPrepared = $db->prepare("UPDATE entity_field_definitions SET `" . $_POST['field'] . "` = ? WHERE `definition_id` = ?;");
    $updateFieldPrepared->bind_param("si", $_POST['newValue'], $typeId);
    $updateFieldSuccess = $updateFieldPrepared->execute();

    if (!$updateFieldSuccess) {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Could not update field."));
        exit();
    }

    /* Send Response */
    echo json_encode(["success" => true, 'message' => 'Field updated']);

} else if ($_POST['action'] === 'DELETE') {
    /* Delete Field */
    $deleteFieldPrepared = $db->prepare("DELETE FROM entity_field_definitions WHERE `definition_id` = ?;");
    $deleteFieldPrepared->bind_param("i", $typeId);
    $deleteFieldSuccess = $deleteFieldPrepared->execute();

    if (!$deleteFieldSuccess) {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Could not delete field."));
        exit();
    }

    /* Send Response */
    echo json_encode(["success" => true, 'message' => 'Field deleted']);

} else if ($_POST['action'] === 'NEW') {
    /* Add Field */
    $addFieldPrepared = $db->prepare("INSERT INTO entity_field_definitions (`label`, `placeholder`, `section`, `type`, `max_length`, `entity_type_id`) VALUES (?, ?, ?, ?, ?, ?);");
    $addFieldPrepared->bind_param("ssssii", $_POST['label'], $_POST['placeholder'], $_POST['section'], $_POST['type'], $_POST['max_length'], $typeId);
    $addFieldSuccess = $addFieldPrepared->execute();

    if (!$addFieldSuccess) {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Could not add field."));
        exit();
    }

    /* Send Response */
    echo json_encode(["success" => true, 'message' => 'Field Added']);

} else {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Invalid action parameter"));
    die();
}