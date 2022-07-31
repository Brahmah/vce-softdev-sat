<?php
/**
 * Save changes to an area.
 *
 * This endpoint will update an areas name and description.
 * It will then return the updated area prior to adding an
 * activity item to the database.
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
if ($_POST['name'] === null) {
    http_response_code(400);
    echo json_encode(array("message" => "Missing name parameter"));
    die();
}
if ($_POST['description'] === null) {
    http_response_code(400);
    echo json_encode(array("message" => "Missing description parameter"));
    die();
}

/* Save Area */
$updateFieldPrepared = $db->prepare("UPDATE areas SET `name` = ?, `description` = ? WHERE `id` = ?");
$updateFieldPrepared->bind_param("ssi", $_POST['name'], $_POST['description'], $areaId);
$updateFieldSuccess = $updateFieldPrepared->execute();

if (!$updateFieldSuccess) {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Could not update area."));
    exit();
}

/* Get Area */
$areaPrepared = $db->prepare("SELECT * FROM areas WHERE `id` = ?");
$areaPrepared->bind_param("i", $areaId);
$area = $areaPrepared->execute();
$areaResult = $areaPrepared->get_result();

if (!$area) {
    http_response_code(404);
    echo json_encode(array("success" => false, "message" => "Area not found."));
    exit();
}

$areaObj = new Area($areaResult->fetch_assoc(), false);

/* Send Response */
echo json_encode($areaObj);

/* Add Activity Item */
include './API/Helpers/addActivityItem.php';
try {
    addActivityItem(
        $db,
        'user',
        $_SESSION['user_id'],
        ':user :verb :area',
        [
            'user' => [
                'text' => $_SESSION['username'],
                'href' => '/SAT_BRH/settings'
            ],
            'verb' => [
                'text' => 'updated',
                'color' => '#673ab7'
            ],
            'area' => [
                'text' => $areaObj->label,
                'href' => '/SAT_BRH/areas'
            ],
        ]
    );
} catch (Exception $e) {
    // Do nothing, bad luck
}