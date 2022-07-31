<?php
/**
 * Get an entity type
 *
 * Get field definitions for an entity type along with the entity type itself.
 * The field definitions describe the fields that are available for each entity type.
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

/* Get Entity Types */
$entityTypePrepared = $db->prepare("SELECT * FROM entity_types where entity_type_id = ?;");
$entityTypePrepared->bind_param("s", $typeId);
$entityTypePrepared->execute();
$entityTypeResult = $entityTypePrepared->get_result();
$rawDbEntityType = $entityTypeResult->fetch_assoc();

if (!$rawDbEntityType) {
//    http_response_code(404);
    echo json_encode(array("success" => false, "message" => "Entity Type not found."));
    exit();
}

/* Get Entity Field Definitions */
$entityTypeDefinitionsPrepared = $db->prepare("SELECT * FROM entity_field_definitions where entity_type_id = ?;");
$entityTypeDefinitionsPrepared->bind_param("s", $typeId);
$entityTypeDefinitionsPrepared->execute();
$entityTypeDefinitionsResult = $entityTypeDefinitionsPrepared->get_result();
$rawDbEntityTypeDefinitions = $entityTypeDefinitionsResult->fetch_all(MYSQLI_ASSOC);

/* Send to client */
$result = [
    ...$rawDbEntityType,
    "type_definitions" => $rawDbEntityTypeDefinitions,
    "count" => $entityTypeDefinitionsResult->num_rows,
];

/* Send to client */
http_response_code(200);
echo json_encode($result);
