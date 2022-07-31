<?php
/**
 * Get an entity type
 *
 * Get entity type information for all entity types.
 * Also includes some metrics for the entity type like the
 * number of entities in the entity type and the number of
 * field definitions for the entity type.
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

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Get Entity Types */
$entityTypesResult = $db->query("SELECT * FROM entity_types;");
$entityTypes = array();
if ($entityTypesResult->num_rows > 0) {
    while ($row = $entityTypesResult->fetch_assoc()) {
        $row['created_date_friendly'] = date('D j M o, g:i a', (int)$row['type_created_timestamp']);
        $entityTypes[] = $row;
    }
}

/* Get Summary Of Entity Types */
$entityTypesSummaryResult = $db->query("SELECT type_id, COUNT(*) FROM entities GROUP BY type_id;");
$entityTypesSummary = array();
if ($entityTypesSummaryResult->num_rows > 0) {
    while ($row = $entityTypesSummaryResult->fetch_assoc()) {
        $entityTypesSummary[] = $row;
    }
}

/* Get Summary Of Entity Type Definitions */
$entityTypeDefinitionsSummaryResult = $db->query("SELECT entity_type_id, COUNT(*) FROM entity_field_definitions GROUP BY entity_type_id;");
$entityTypeDefinitionsSummary = array();
if ($entityTypeDefinitionsSummaryResult->num_rows > 0) {
    while ($row = $entityTypeDefinitionsSummaryResult->fetch_assoc()) {
        $entityTypeDefinitionsSummary[] = $row;
    }
}

/* Set Summary Of Entity Types & Definitions */
foreach ($entityTypes as $key => $entityType) {
    $entityTypes[$key]['entity_count'] = 0;
    $entityTypes[$key]['definition_count'] = 0;
    foreach ($entityTypesSummary as $entityTypeSummary) {
        if ($entityType['entity_type_id'] == $entityTypeSummary['type_id']) {
            $entityTypes[$key]['entity_count'] = (int)$entityTypeSummary['COUNT(*)'];
        }
    }
    foreach ($entityTypeDefinitionsSummary as $entityTypeDefinitionSummary) {
        if ($entityType['entity_type_id'] == $entityTypeDefinitionSummary['entity_type_id']) {
            $entityTypes[$key]['definition_count'] = (int)$entityTypeDefinitionSummary['COUNT(*)'];
        }
    }
}

/* Send to client */
$result = [
    "entity_types" => $entityTypes,
    "count" => count($entityTypes)
];

/* Send to client */
http_response_code(200);
echo json_encode($result);

