<?php
/* Imports */
include_once realpath(dirname(__FILE__)) . '/../Models/Entity.php';

/* URI Parameters */
/** @var string $entityId URI Parameter */

/* Response Headers */
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

/* Continue */
$entity = new Entity($entityId);
echo json_encode( $entity );