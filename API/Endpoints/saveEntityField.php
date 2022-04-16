<?php

use Models\Entity;

include_once '../Models/EntityField.php';
include_once '../Models/Entity.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$entity = new Entity($_GET['entityId']);
$fields = $entity->fields;
$relevantFields = array_filter($fields, function ($field) {
    return $field->htmlId == $_GET['fieldId'];
});

if (count($relevantFields) == 0) {
    $response = [
        'success' => false,
        'message' => 'Field Not Found',
        'relevantField' => null,
        'cssColor' => 'red',
        'cssStatus' => generateCssStatus($_GET['fieldId'], 'Field Not Found', 'red')
    ];
    echo json_encode($response);
} else {
    $relevantField = array_values($relevantFields)[0];
    $validationResult = $relevantField->validate($_GET['value']);

    $response = [
        'success' => $validationResult->valid,
        'message' => $validationResult->message,
        'relevantField' => $relevantField,
        'cssColor' => $validationResult->color,
        'cssStatus' => '',
    ];

    $response['cssStatus'] = generateCssStatus($_GET['fieldId'], $response['message'], $response['cssColor']);
    echo json_encode($response);
    return;
}

function generateCssStatus($entityId, $message, $color): string
{

    return "<style>#field-label-status-" . $entityId . ":after {
            content: '" . $message . "';
            color: " . $color . ";
            position: absolute;
            font-size: 11px;
            display: block;
        }</style>";
}