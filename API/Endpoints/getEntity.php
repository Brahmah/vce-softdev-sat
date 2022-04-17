<?php
/* Imports */
include './API/Helpers/initDatabaseConnection.php';
include './API/Models/Entity.php';

/* Database */
$db = getConnection();

/* URI Parameters */
/** @var string $entityId URI Parameter */

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Get and send */
$sql = "SELECT * FROM entities WHERE id =" . $entityId; // this is an sql injection waiting to happen. lol.
$result = $db->query($sql);
$raw_db_entity = $result->fetch_assoc();

if ($raw_db_entity == null) {
    include './API/Endpoints/Errors/404.php';
    exit();
} else {
    http_response_code(200);
    $entity = new Entity($raw_db_entity);
    echo json_encode( $entity );
}