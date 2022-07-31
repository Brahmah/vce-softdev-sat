<?php
/**
 * Get options for an entity field.
 *
 * Some entity fields have a limited number of options. This endpoint returns
 * the available options for a given field. The options are returned as an array
 * of objects in a consistent schema across differing field types.
 *
 * If the field options are for an area, the options will be for the area's children.
 * The label property will be the area's name with an indented prefix multiplied by the area's
 * depth in the hierarchy.
 *
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

sleep(1); // simulate slow response

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

/* Find Relevant Entity Field */
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
        'options' => null,
    ];
    http_response_code(404);
    echo json_encode($response);
    exit();
}

$relevantField = array_values($relevantFields)[0];

/* Get options depending on the field type */
$options = array();

switch ($relevantField->type) {
    case 'device_type':
        $options = $db->query("SELECT * FROM entity_types")->fetch_all(MYSQLI_ASSOC);
        $options = array_map(function ($thisOption) use ($relevantField) {
            return [
                'label' => $thisOption['type_label'],
                'value' => $thisOption['entity_type_id'],
                'isDefault' => $thisOption['entity_type_id'] == $relevantField->value,
            ];
        }, $options);
        break;
    case 'area':
        $areas = $db->query("SELECT * FROM areas where id <> 0;")->fetch_all(MYSQLI_ASSOC);
        function findChildren($parent_id, &$areas_list, &$children, $depth, $relevantField)
        {
            foreach ($areas_list as $area) {
                if ($area['parent_id'] == $parent_id) {
                    // Set the depth of this area
                    $area['depth'] = $depth;
                    // Set some properties for the area
                    $area['label'] = str_repeat(' ', $depth * 4) . '> ' . $area['name'];
                    $area['value'] = $area['id'];
                    $area['isDefault'] = $area['id'] == $relevantField->value;
                    // Add to children
                    $children[] = $area;
                    // Remove from areas list
                    $key = array_search($area['id'], array_column($areas_list, 'id'));
                    unset($areas_list[$key]);
                    // Recurse
                    if ($area['id'] != $parent_id) {
                        // :sweats:, avoid infinite recursion. Don't ask how i found out...
                        findChildren($area['id'], $areas_list, $children, $depth + 1, $relevantField);
                    }
                }
            }
        }
        findChildren(0, $areas, $options, 0, $relevantField);
        break;
    default:
        $options = [];
        break;
}

/* Prepare the response */
$response = [
    'success' => true,
    'message' => 'Success',
    'relevantField' => $relevantField,
    'options' => $options,
];

/* Send the response */
echo json_encode($response);