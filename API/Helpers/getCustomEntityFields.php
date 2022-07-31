<?php
/**
 * Get custom entity fields for an entity.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
require_once './API/Models/EntityField.php';

/**
 * @param $db - MySQLi database connection
 * @param $raw_db_entity - array of entity data from the database
 * @return array - array of CustomEntityField objects
 */
function getCustomEntityFields($db, $raw_db_entity): array
{
    $entity_extra_fields_query_prepare = $db->prepare("
        SELECT 
          * 
        FROM 
          entity_field_definitions 
          left join entity_field_values on (
            entity_field_definitions.definition_id = entity_field_values.field_definition_id 
            and entity_field_values.field_parent_entity_id = ?
          ) 
        WHERE 
          entity_type_id = ?;
    ");
    $entity_extra_fields_query_prepare->bind_param("ii", $raw_db_entity['id'], $raw_db_entity['type_id']);
    $entity_extra_fields_query_prepare->execute();
    $entity_extra_fields_query_result = $entity_extra_fields_query_prepare->get_result();
    $raw_db_entity_extra_fields = $entity_extra_fields_query_result->fetch_all(MYSQLI_ASSOC);
    $entity_extra_fields = array();
    foreach ($raw_db_entity_extra_fields as $raw_db_entity_extra_field) {
        $entity_extra_fields[] = new CustomEntityField($raw_db_entity_extra_field, $raw_db_entity['id']);
    }
    return $entity_extra_fields;
}