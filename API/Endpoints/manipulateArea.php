<?php
/**
 * Perform operations on an area or its descendants.
 *
 * ---- Context: ---- determines where the operation is performed on.
 * - Entity: -- will affect the entity.
 * - Area: -- will affect the area.
 *
 * ---- Operations: ---- determines what operation to performed.
 * - add: -- will add the entity to the area.
 * - update: -- will update the entity in the area.
 * - delete: -- will delete the area.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
/* Imports */
require_once './API/Helpers/initDatabaseConnection.php';
require_once './API/Models/Entity.php';
require_once './API/Models/EntityField.php';

/* Database */
$db = getConnection();

/* URI Parameters */
/** @var string $areaId URI Parameter */

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Validate Parameters */
if (!is_numeric($areaId)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "areaId must be a number."));
    die();
}
$required_fields = array("type", "context");
foreach ($required_fields as $field) {
    if ($_POST[$field] === null) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Missing $field parameter"));
        die();
    }
}
$valid_types = array("add", "delete", "update");
if (!in_array($_POST["type"], $valid_types)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Invalid type parameter. Valid types are: " . implode(", ", $valid_types)));
    die();
}

/* Switch context */
if ($_POST['context'] === 'entity' && $_POST['type'] === 'add') {
    /* Add entity to area */
    $insert_prepare = $db->prepare("INSERT INTO entities (`type_id`, `status`, `area_id`, `name`) VALUES (?, ?, ?, ?)");
    $type_id = '0';
    $status = 'Inactive';
    $name = 'New Device';
    $insert_prepare->bind_param("ssis", $type_id, $status, $areaId, $name);
    $insert_success = $insert_prepare->execute();
    if (!$insert_success) {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Could not insert entity."));
        exit();
    }
    $entity_id = $db->insert_id;
    $entity_prepare = $db->prepare("SELECT * FROM entities WHERE `id` = ?");
    $entity_prepare->bind_param("i", $entity_id);
    $entity_result = $entity_prepare->execute();
    if (!$entity_result) {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Could not get entity."));
        exit();
    }
    $entity_result = $entity_prepare->get_result();
    $entity_obj = new Entity($entity_result->fetch_assoc());
    $result = [
        "success" => true,
        "entity" => $entity_obj,
        "message" => "Entity added to area."
    ];
    echo json_encode($result);
} elseif ($_POST['context'] === 'area' && $_POST['type'] === 'add') {
    /* Add child area to area */
    $area_mappings = [
        'campus' => 'building',
        'building' => 'room',
        'room' => 'area',
        'area' => 'area'
    ];
    $area_type = 'area';
    if (isset($area_mappings[$_POST['item_type']])) {
        $area_type = $area_mappings[$_POST['item_type']];
    } else {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Invalid type parameter. Valid types are: " . implode(", ", array_keys($area_mappings))));
        die();
    }
    if ($_POST['item_type'] === 'area') {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Too many levels of nesting."));
        die();
    }
    $insert_prepare = $db->prepare("INSERT INTO areas (`parent_id`, `name`, `description`, `type`) VALUES (?, ?, ?, ?)");
    $name = 'New Area';
    $description = 'New Area Description';
    $insert_prepare->bind_param("isss", $areaId, $name, $description, $area_type);
    $insert_success = $insert_prepare->execute();
    if (!$insert_success) {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Could not insert area."));
        exit();
    }
    $area_id = $db->insert_id;
    $area_prepare = $db->prepare("SELECT * FROM areas WHERE `id` = ?");
    $area_prepare->bind_param("i", $area_id);
    $area_result = $area_prepare->execute();
    if (!$area_result) {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Could not get area."));
        exit();
    }
    $area_result = $area_prepare->get_result();
    $result = [
        "success" => true,
        "area" => $area_result->fetch_assoc(),
        "message" => "Area Created."
    ];
    echo json_encode($result);
} elseif ($_POST['context'] === 'area' && $_POST['type'] === 'delete') {
    /* Check area has no child areas */
    $area_prepare = $db->prepare("SELECT * FROM areas WHERE `parent_id` = ?");
    $area_prepare->bind_param("i", $areaId);
    $area_result = $area_prepare->execute();
    if (!$area_result) {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Could not get area."));
        exit();
    }
    $area_result = $area_prepare->get_result();
    if ($area_result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Area has children. Cannot delete."));
        exit();
    }
    /* Check area has no child entities */
    $entity_prepare = $db->prepare("SELECT * FROM entities WHERE `area_id` = ?");
    $entity_prepare->bind_param("i", $areaId);
    $entity_result = $entity_prepare->execute();
    if (!$entity_result) {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Could not get entities."));
        exit();
    }
    $entity_result = $entity_prepare->get_result();
    if ($entity_result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Area has devices. Cannot delete."));
        exit();
    }
    /* Delete area */
    $delete_prepare = $db->prepare("DELETE FROM areas WHERE `id` = ?");
    $delete_prepare->bind_param("i", $areaId);
    $delete_success = $delete_prepare->execute();
    if (!$delete_success) {
        http_response_code(500);
        echo json_encode(array("success" => false, "message" => "Could not delete area."));
        exit();
    }
    $result = [
        "success" => true,
        "message" => "Area Deleted."
    ];
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Invalid context parameter"));
    die();
}