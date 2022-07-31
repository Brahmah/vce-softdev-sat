<?php
/**
 * Get all entities
 *
 * This endpoint returns all entities in the system.
 * The raw entities are first mapped and converted to Entity classes.
 *
 * This will also join the entities to their respective areas and types.
 *
 * NOTE: This will not return custom entity fields. To get custom entity fields, use the getEntity endpoint.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
/* Imports */
include './API/Helpers/initDatabaseConnection.php';
include './API/Models/Entity.php';

/* Database */
$db = getConnection();

/* URI Parameters */
/** @var string $entityId URI Parameter */

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Get & send */
$sql = "SELECT 
  entities.*, 
  areas.name AS area_name, 
  areas.type AS area_type, 
  areas.description AS area_description, 
  areas.parent_id as area_parent_id, 
  entity_types.type_label 
FROM 
  entities 
  INNER JOIN areas ON entities.area_id = areas.id 
  INNER JOIN entity_types ON entities.type_id = entity_types.entity_type_id;
";

$result = $db->query($sql);

$entities = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entities[] = new Entity($row);
    }
}

$allAreas = array_map(function ($entity) {
    return $entity->area->id;
}, $entities);
$allAreas = array_unique($allAreas);

$response = [
    "entities" => $entities,
    'areasCount' => count($allAreas),
    "count" => count($entities),
];

echo json_encode( $response );

