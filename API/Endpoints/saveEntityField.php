<?php
/**
 * Save changes to an entity field.
 *
 * This part of the app is the most automagical and will be the most
 * complicated to implement. (it actually wasn't)
 *
 * This endpoint will update an entity field, the field will be located by its
 * field id and parent entity id. It will then return an encoded class object of
 * the relevant entity field. This class will be used to validate the updated field.
 *
 * The endpoint response will also include the identified entity prior to any changes
 * made by the call. After validation, the entity will be updated or the field if it
 * was a custom field.
 *
 * Finally, an activity item will be added to the database to track the changes.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
/* Imports */
require_once './API/Helpers/initDatabaseConnection.php';
require_once './API/Models/Entity.php';
require_once './API/Models/EntityField.php';
require_once './API/Helpers/getCustomEntityFields.php';

/* Database */
$db = getConnection();

/* URI Parameters */
/** @var string $entityId URI Parameter */
/** @var string $fieldId URI Parameter */

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Get Existing Entity */
$rawEntityResult = $db->query("SELECT * FROM entities WHERE id =" . $db->real_escape_string($entityId)); // this is an sql injection waiting to happen. lol.
$rawDbEntity = $rawEntityResult->fetch_assoc();

if ($rawDbEntity == null) {
    http_response_code(404);
    echo json_encode(array("message" => "Entity not found."));
    exit();
}

/* Get entity extra fields */
$customEntityFields = getCustomEntityFields($db, $rawDbEntity);
/* Save Entity Field */
$entity = new Entity($rawDbEntity, $customEntityFields);
$fields = $entity->fields;
$relevantFields = array_filter($fields, function ($thisField) use ($fieldId) {
    return $thisField->htmlId == $fieldId;
});

if (count($relevantFields) == 0) {
    $response = [
        'success' => false,
        'message' => 'Field Not Found',
        'relevantField' => null,
        'cssColor' => 'red',
        'cssStatus' => generateCssStatus($fieldId, 'Field Not Found', 'red'),
        'entityPriorToUpdate' => $entity,
        'updatedFieldId' => $fieldId,
    ];
    http_response_code(404);
    echo json_encode($response);
    exit();
}

// Get the field
$relevantField = array_values($relevantFields)[0];
// Validate the field
$validationResult = $relevantField->validate($_POST['value']);
// Prepare the response
$response = [
    'success' => $validationResult->valid,
    'message' => $validationResult->message,
    'relevantField' => $relevantField,
    'cssColor' => $validationResult->color,
    'cssStatus' => generateCssStatus($fieldId, $validationResult->message, $validationResult->color),
    'entityPriorToUpdate' => $entity,
    'updatedFieldId' => $fieldId,
];
// Update the field if validating was successful
if ($validationResult->valid) {
    $updateFieldPrepare = null;
    if ($relevantField->isCustom) {
        // Update the custom field
        $updateFieldPrepare = $db->prepare("INSERT INTO entity_field_values (field_definition_id, field_value, field_parent_entity_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE field_value = ?");
        $updateFieldPrepare->bind_param("isis", $relevantField->definitionId, $_POST['value'], $entityId, $_POST['value']);
    } else {
        // While the bellow query might seem like an SQL injection waiting to happen, it is not. There's no way dbFieldName can be a user-supplied value.
        $updateFieldPrepare = $db->prepare("UPDATE entities SET " . $relevantField->dbFieldName . " = ? WHERE id = ?");
        $updateFieldPrepare->bind_param("ss", $_POST['value'], $entityId);
    }
    $updateFieldPrepare->execute();
    $updateFieldSuccess = $updateFieldPrepare->affected_rows > 0;
    if (!$updateFieldSuccess){
        $response['success'] = false;
        $response['message'] = 'Failed To Update Field (DB Error)';
        $response['cssColor'] = 'red';
        $response['cssStatus'] = generateCssStatus($fieldId, $response['message'], 'red');
    }
}
// Encode and send the response
echo json_encode($response);

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

/* Add Activity Item */
include './API/Helpers/addActivityItem.php';
try {
    addActivityItem(
        $db,
        'entity',
        $entity->id,
        ":user :verb :entity -> :field",
        [
            'user' => [
                'text' => $_SESSION['username'],
                'href' => '/SAT_BRH/settings'
            ],
            'verb' => [
                'text' => 'updated',
                'color' => '#673ab7'
            ],
            'entity' => [
                'text' => $entity->name != null && $entity->name != '' ? $entity->name : $entity->ip_address,
                'href' => '/SAT_BRH/devices/' . $entity->id
            ],
            'field' => [
                'text' => $relevantField->label,
                'color' => '#607d8b'
            ],
            'newValue' => [
                'text' => substr($_POST['value'], 0, 40) . '...',
                'color' => '#607d8b'
            ]
        ]
    );
} catch (Exception $e) {
    // Do nothing, bad luck
}