<?php
/**
 * Fetches nodes of an area.
 *
 * Areas are defined by a list of nodes. Each node is a list of children.
 * These children can be other areas or entities. This endpoint returns all child nodes of an area.
 *
 * (Note: Child NODES, with an O!! Don't be that one classmate that reported me for this.)
 *
 * This endpoint will not always expand the node hierarchy, in some cases it will only return the
 * immediate children of the area; whether these are areas or entities.
 *
 * If the area is not found, this endpoint will return a 404.
 * If the area is theoretically an orphan, the parent area will be null. However, this should almost
 * never happen as an area should always have a parent except for the root area. The system ensures children
 * are never orphaned.
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
/** @var string $areaId URI Parameter */
/** @var bool $expand Expand Parameters */
$expandChildren = isset($_GET['expand']) ?? false;

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Get All Areas */
// In the long run, this is an inefficient query as getting child nodes of a node won't require
// other unrelated nodes to also be queried. However, it's a simple query and the performance
// impact is negligible within this context. Furthermore, the frontend currently will only query from
// the root node.
$areasResult = $db->query("SELECT * FROM areas");
$areaNodes = array();
while ($row = $areasResult->fetch_assoc()) {
    $areaNodes[] = new Area($row, false);
}

/* Get All Entities */
$query = "SELECT * FROM entities";
if (!$expandChildren) {
    $query .= " WHERE area_id = " . $db->real_escape_string($areaId);
}
$entitiesResult = $db->query($query);
$entityNodes = array();
while ($row = $entitiesResult->fetch_assoc()) {
    $entityNodes[] = new Entity($row);
}

/* Get Area Itself */
$thisAreaKey = array_search($areaId, array_column($areaNodes, 'id'));
$parentArea = null;
$thisArea = null;

if ($thisAreaKey === false) {
    http_response_code(404);
    echo json_encode(array("error" => "Area not found"));
    exit();
} else {
    // Set this area
    $thisArea = $areaNodes[$thisAreaKey];
    // Set parent area
    $parentAreaKey = array_search($thisArea->parent_id, array_column($areaNodes, 'id'));
    if ($parentAreaKey !== false && $thisArea->parent_id !== $thisArea->id) {
        $parentArea = $areaNodes[$parentAreaKey];
    }
    // Remove both areas from the list as they will definitely not be child nodes
    array_splice($areaNodes, $thisAreaKey, 1);
    if ($parentArea != null) {
        array_splice($areaNodes, $parentAreaKey, 1);
    }
    //
}

/* Get Children Recursive */
$child_nodes = getChildNodes($thisArea->id, $areaNodes, $entityNodes, $expandChildren, 0);

/**
 * Recursive function to get all child nodes of a given area
 * @param $forAreaId int Area ID
 * @param $areaNodes array Area Nodes
 * @param array $entityNodes Entity Nodes
 * @param $expandChildren bool Should get child nodes of child nodes
 * @param int $depth
 * @return array
 */
function getChildNodes(int $forAreaId, array $areaNodes, array $entityNodes, bool $expandChildren, int $depth): array
{
    $child_nodes = array();
    // Get all entities for this area
    if ($expandChildren) {
        $child_nodes = getChildEntityNodes($forAreaId, $entityNodes);
    }
    // Get all areas that are children of the given area
    foreach ($areaNodes as $area) {
        if ($area->parent_id == $forAreaId) {
            if ($expandChildren) {
                // Get child nodes of child nodes for areas
                $area->children = getChildNodes($area->id, $areaNodes, $entityNodes, true, $depth + 1);
                // Count the number of child nodes
                $area->children_count = count($area->children);
            }
            $area->open = $depth < 1;
            $child_nodes[] = $area;
            // Remove from $areaNodes as it will not be a child node anymore
            $key = array_search($area, $areaNodes);
            array_splice($areaNodes, $key, 1);
        }
    }
    // Return child nodes
    return $child_nodes;
}

/**
 * Get all entities that are children of a given area
 * @param int $areaId
 * @param array $entityNodes
 * @return array
 */
function getChildEntityNodes(int $areaId, array $entityNodes): array
{
    $entities = array();
    foreach ($entityNodes as $entity) {
        if ($entity->area_id == $areaId) {
            // Append
            $entities[] = [
                'label' => empty($entity->name) ? $entity->ip_address : $entity->name,
                'icon' => "⚛️",
                'id' => (string)$entity->id, // Frontend Bug: https://github.com/bartaxyz/react-tree-list/issues/28
                'type' => "entity",
            ];
            // Remove entity from entity list
            $entityKey = array_search($entity, $entityNodes);
            if ($entityKey !== false) {
                array_splice($entityNodes, $entityKey, 1);
            }
        }
    }
    return $entities;
}

/* Construct Full Response */
$response = [
    "area" => $thisArea,
    'parent_area' => $parentArea,
    'children' => $child_nodes,
    'expand_children' => $expandChildren
];

/* Send Response */
// $_GET['dontRespond'] for debug purposes
if (!isset($_GET['dontRespond'])) {
    http_response_code(200);
    echo json_encode($response);
}