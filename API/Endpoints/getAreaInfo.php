<?php
// This endpoint is a dummy endpoint for the completion of the SAT as it is not included in the
// Software Requirements Specification and therefore, not assessed in the final grade.
// The code here is not meant to be used in production and very s**t. Only written to make the UI
// Look like it has this data.

/* Headers */
header('Content-Type: application/json');

/* Get area nodes */
$_GET['expand'] = true;
$_GET['dontRespond'] = true;
require_once 'getAreaNodes.php';

/* Count Total Entities */
$total_entities = countAreaChildren($child_nodes);

/* Construct Full Response */
$online_offline_entities = getRandomOnlineAndOfflineEntities($total_entities);
$response = [
    "totalEntities" => $total_entities,
    'onlineEntities' => $online_offline_entities[0],
    'offlineEntities' => $online_offline_entities[1],
];

/* Send Response */
http_response_code(200);
echo json_encode($response);

/* Helpers */
function countAreaChildren(array $nodes): int
{
    $count = 0;
    foreach ($nodes as $node) {
        if (gettype($node) == "array" && $node['type'] == 'entity') {
            $count++;
        }
        if (isset($node->children)) {
            $count += countAreaChildren($node->children);
        }
    }
    return $count;
}

function getRandomOnlineAndOfflineEntities(int $total_entities): array
{
    // Return from session cache if already generated
    $last_generated_value_session_key = 'last_random_generated_online_offline_breakdown_for_total_' . $total_entities;
    if (isset($_SESSION[$last_generated_value_session_key])) {
        return $_SESSION[$last_generated_value_session_key];
    }
    // Else generate again
    $online_entities = 0;
    $offline_entities = 0;
    for ($i = 0; $i < $total_entities; $i++) {
        if (rand(0, 3) != 0) {
            $online_entities++;
        } else {
            $offline_entities++;
        }
    }
    $_SESSION[$last_generated_value_session_key] = [$online_entities, $offline_entities];
    return $_SESSION[$last_generated_value_session_key];
}