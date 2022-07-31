<?php
/**
 * Get an entity by ID.
 *
 * This endpoint will return an entity including all custom fields.
 * The entity will be of Entity class.
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
require_once './API/Helpers/getCustomEntityFields.php';

/* Database */
$db = getConnection();

/* URI Parameters */
/** @var string $entityId URI Parameter */

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

/* Get entity extra fields */
$custom_entity_fields = getCustomEntityFields($db, $raw_db_entity);

/* Send Response */
http_response_code(200);
$entity = new Entity($raw_db_entity, $custom_entity_fields);
echo json_encode( $entity );
