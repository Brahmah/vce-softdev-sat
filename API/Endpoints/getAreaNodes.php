<?php
/* Imports */
include './API/Helpers/initDatabaseConnection.php';
include './API/Models/Entity.php';
include './API/Models/Area.php';

/* Database */
$db = getConnection();

/* URI Parameters */
/** @var string $areaId URI Parameter */

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Get Area Itself */
$areaResult = $db->query("SELECT * FROM areas WHERE id = '$areaId'");
$parentArea = new Area($areaResult->fetch_assoc());

/* Get Area Nodes */
$result = $db->query("SELECT * FROM areas WHERE parent_id =" . $areaId); // this is an sql injection waiting to happen. lol.
$areaNodes = array();
while ($row = $result->fetch_assoc()) {
    $areaNodes[] = new Area($row);
}

/* Construct Full Response */
$response = [
    'parent_area' => $parentArea,
    'area_nodes' => $areaNodes
];

/* Send Response */
http_response_code(200);
echo json_encode($response);
