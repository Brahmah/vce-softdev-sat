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

/* Get & send */
$sql = "SELECT * FROM entities";
$result = $db->query($sql);

$entities = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entities[] = new Entity($row);
    }
}

$response = [
    "entities" => $entities,
    "count" => count($entities),
    "csrfToken" => $_SESSION["csrf"],
    "importantMessage" => 'Never gonna give you up, never gonna let you down, never gonna run around and desert you, never gonna make you cry, never gonna say goodbye, never gonna tell a lie and hurt you.'
];

echo json_encode( $response );

