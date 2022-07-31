<?php
/**
 * Fetches activity items from the database.
 *
 * Activity items are created within differing contexts.
 *
 * Some examples of which are ["area", "entity", "all", "alerts"].
 *
 * When a new activity item is created, it has also got an identifier
 * not necessarily unique but used in conjunction with the context to
 * determine what kind of activity item it is and to what parent it belongs.
 *
 * For example, if a new area is created, it will have an identifier
 * of the area's id and the context of "area". The context will be used
 * in conjunction with the identifier to determine what items are returned.
 *
 * When activity items are fetched with a context of "area", the system will automagically
 * fetch not only all activity items that belong to the area but also all activity items that belong
 * to the area's child areas.
 *
 * Finally, when activity items are fetched with a context of "all", the system will automagically
 * fetch all activity items except for those with a context of "alerts". These will accessible only
 * by the alert's context.
 *
 * We generally limit the number of activity items returned to 200 in most cases. Alerts are the exception limited to 5
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
/* imports */
require_once './API/Helpers/initDatabaseConnection.php';
require_once './API/Models/Entity.php';
require_once './API/Models/Area.php';
require_once './API/Models/ActivityItem.php';

/* database */
$db = getConnection();

/* uri parameters */
/** @var string $context uri parameter */
/** @var string $itemId uri parameter */

/* headers */
header("content-type: application/json; charset=utf-8");

/* validate context */
$validContexts = ["area", "entity", "all", "alerts"];
if (!in_array($context, $validContexts)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Invalid context parameter. Valid values are: " . implode(", ", $validContexts)));
    die();
}

/* switch context */
if ($context == "area") {
    /* get area */
    $areaPrepared = $db->prepare("select * from areas where id = ?");
    $areaPrepared->bind_param("i", $itemId);
    $areaSuccess = $areaPrepared->execute();
    $areaResult = $areaPrepared->get_result();
    $rawDbArea = $areaResult->fetch_assoc();
    if (!$rawDbArea) {
        http_response_code(404);
        echo json_encode(array("success" => false, "message" => "area not found."));
        exit();
    }
    $area = new Area($rawDbArea, false);
    /* get all areas */
    $areasQuery = $db->query("select id, parent_id from areas");
    $areasList = array();
    while ($areaRow = $areasQuery->fetch_assoc()) {
        $areasList[] = $areaRow;
    }
    /* find children */
    function findChildren($parentId, &$areasList, &$children)
    {
        foreach ($areasList as $area) {
            if ($area['parent_id'] == $parentId) {
                // add to children
                $children[] = $area['id'];
                // remove from areas list
                $key = array_search($area['id'], array_column($areasList, 'id'));
                unset($areasList[$key]);
                // recurse
                if ($area['id'] != $parentId) {
                    // :sweats:, avoid infinite recursion. don't ask how i found out...
                    findChildren($area['id'], $areasList, $children);
                }
            }
        }
    }
    $areaChildren = array($itemId); // start with the area itself
    findChildren($area->id, $areasList, $areaChildren);
    /* find all children in area */
    // rem: some classmates read my code and raised concerns; believing i'm writing a program to find children in my area. this is not deliberate.
    $entitiesInChildrenQuery = $db->query("select id from entities where area_id in (" . implode(',', $areaChildren) . ")");
    $entitiesInChildrenList = array();
    while ($entityInChildrenList = $entitiesInChildrenQuery->fetch_assoc()) {
        $entitiesInChildrenList[] = $entityInChildrenList['id'];
    }
    /* get all activity where in children */
    $activityQuery = $db->query("select * from activity where `context` = 'entity' and item_id in (" . implode(',', $entitiesInChildrenList) . ") order by timestamp desc limit 200;");
    $activityList = array();
    while ($activityRow = $activityQuery->fetch_assoc()) {
        $activityList[] = new ActivityItem($activityRow);
    }
    /* send to client */
    $result = [
        "area" => $area,
        "activity" => $activityList,
        "count" => count($activityList)
    ];

    http_response_code(200);
    echo json_encode($result);
} else if ($context == "entity") {
    /* get entity */
    $entityQueryPrepare = $db->prepare("select * from entities where id = ? limit 200;");
    $entityQueryPrepare->bind_param("i", $itemId);
    $entityQuerySuccess = $entityQueryPrepare->execute();
    $entityQueryResult = $entityQueryPrepare->get_result();
    $rawDbEntity = $entityQueryResult->fetch_assoc();
    if ($rawDbEntity == null) {
        require_once './API/Endpoints/Errors/404.php';
        exit();
    }
    $entity = new Entity($rawDbEntity);

    /* get activity */
    $activityQueryPrepare = $db->prepare("select * from activity where `item_id` = ? and `context` = ? order by timestamp desc limit 200;");
    $activityQueryPrepare->bind_param("is", $entity->id, $context);
    $activityQueryPrepare->execute();
    $activityItemsResult = $activityQueryPrepare->get_result();
    $activityItems = array();
    while ($activityItemRaw = $activityItemsResult->fetch_assoc()) {
        $activityItems[] = new ActivityItem($activityItemRaw);
    }
    /* send to client */
    $result = [
        "entity" => $entity,
        "activity" => $activityItems,
        "count" => count($activityItems)
    ];

    http_response_code(200);
    echo json_encode($result);
} else if ($context == "all") {
    /* get activity */
    $activityQueryPrepare = $db->prepare("select * from activity where context <> 'alert' order by timestamp desc limit 200;");
    $activityQueryPrepare->execute();
    $activityItemsResult = $activityQueryPrepare->get_result();
    $activityItems = array();
    while ($activityItemRaw = $activityItemsResult->fetch_assoc()) {
        $activityItems[] = new ActivityItem($activityItemRaw);
    }
    /* send to client */
    $result = [
        "all" => true,
        "activity" => $activityItems,
        "count" => count($activityItems)
    ];

    http_response_code(200);
    echo json_encode($result);
} else if ($context == "alerts") {
    /* get alerts */
    $activityQueryPrepare = $db->prepare("select * from activity where `context` = 'alert' order by timestamp desc limit 5;");
    $activityQueryPrepare->execute();
    $activityItemsResult = $activityQueryPrepare->get_result();
    $activityItems = array();
    while ($activityItemRaw = $activityItemsResult->fetch_assoc()) {
        $activityItems[] = new ActivityItem($activityItemRaw);
    }
    /* send to client */
    $result = [
        "activity" => $activityItems,
        "count" => count($activityItems)
    ];

    http_response_code(200);
    echo json_encode($result);
}
