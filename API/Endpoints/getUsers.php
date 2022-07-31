<?php
/**
 * Get all users authorized to login to the system.
 *
 * This endpoint returns all users authorized to login to the system.
 * This will also return the user's roles and permissions.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
/* Imports */
require_once './API/Helpers/initDatabaseConnection.php';

/* Database */
$db = getConnection();

/* Headers */
header("Content-Type: application/json; charset=UTF-8");

/* Get users */
$users_query_prepare = $db->prepare("
SELECT 
  users.id, 
  users.username, 
  users.role, 
  users.created_timestamp, 
  users.created_by_user_id,
  b.username as created_by_user_username,
  b.role as created_by_user_role
FROM 
  users 
  left join users b on b.id = users.created_by_user_id;
");
$users_query_prepare->execute();
$users_query_result = $users_query_prepare->get_result();
$raw_db_users = $users_query_result->fetch_all(MYSQLI_ASSOC);

if ($raw_db_users == null) {
    include './API/Endpoints/Errors/500.php';
    exit();
}

$users = array_map(function ($user) {
    return [
        'id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
        'created' => [
            'timestamp' => $user['created_timestamp'],
            'date' => date('Y-m-d H:i:s', $user['created_timestamp']),
            'friendly' => date('D j M o, g:i a', (int)$user['created_timestamp']),
        ],
        'creator' => [
            'id' => $user['created_by_user_id'],
            'username' => $user['created_by_user_username'],
            'role' => $user['created_by_user_role']
        ]
    ];
}, $raw_db_users);

/* Send Response */
http_response_code(200);
echo json_encode([
    'users' => $users,
    'count' => count($users)
]);