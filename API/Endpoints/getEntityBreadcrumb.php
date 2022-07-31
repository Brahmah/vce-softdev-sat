<?php
/**
 * Get breadcrumb for an entity.
 *
 * Breadcrumbs are a list of nodes that lead to the current entity.
 * This endpoint also returns the current entity.
 *
 * A generic 404 will be returned if the entity does not exist.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
/* Imports */
require_once './API/Helpers/initDatabaseConnection.php';
require_once './API/Models/Entity.php';

/* Database */
$db = getConnection();

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Get entity */
$entity_query_prepare = $db->prepare("SELECT * FROM entities WHERE id = ?");
$entity_query_prepare->bind_param("s", $entityId);
$entity_query_prepare->execute();
$entity_query_result = $entity_query_prepare->get_result();
$raw_db_entity = $entity_query_result->fetch_assoc();

if ($raw_db_entity == null) {
    include './API/Endpoints/Errors/404.php';
    exit();
}

/* Get all areas */
$areas_query_prepare = $db->prepare("SELECT * FROM areas");
$areas_query_prepare->execute();
$areas_query_result = $areas_query_prepare->get_result();
$raw_db_areas = $areas_query_result->fetch_all(MYSQLI_ASSOC);

if ($raw_db_areas == null) {
    include './API/Endpoints/Errors/500.php';
    exit();
}

/* Get area for this entity */
$area_for_entity = $raw_db_areas[array_search($raw_db_entity['area_id'], array_column($raw_db_areas, 'id'))];

/* declare breadcrumbs */
$breadcrumbs = [];

/* Add entity to breadcrumbs */
$breadcrumbs[] = [
    'id' => 'entity:' . $raw_db_entity['id'],
    'label' => $raw_db_entity['name'],
    'href' => '/SAT_BRH/devices/' . $raw_db_entity['id']
];

/* Get parent areas */
function addParentAreaToBreadcrumbs($raw_db_areas, $area, &$breadcrumbs) {
    $breadcrumbs[] = [
        'id' => 'area:' . $area['id'],
        'label' => $area['name'],
        'href' => '/SAT_BRH/areas',
    ];
    if ($area['parent_id'] != null) {
        $parent_area = $raw_db_areas[array_search($area['parent_id'], array_column($raw_db_areas, 'id'))];
        addParentAreaToBreadcrumbs(
            $raw_db_areas,
            $parent_area,
            $breadcrumbs
        );
    }
}

addParentAreaToBreadcrumbs($raw_db_areas, $area_for_entity, $breadcrumbs);

/* Add main area to breadcrumbs */
$breadcrumbs[] = [
    'id' => 'area:' . -1,
    'label' => 'All Areas',
    'href' => '/SAT_BRH/areas',
];

/* Add some extra properties to crumbs */
foreach ($breadcrumbs as &$breadcrumb) {
    $breadcrumb['isActive'] = $breadcrumb['id'] == 'entity:' . $raw_db_entity['id'];
}

/* Send Response */
http_response_code(200);
echo json_encode([
    'crumbs' => array_reverse($breadcrumbs),
    'entity' => new Entity($raw_db_entity),
]);