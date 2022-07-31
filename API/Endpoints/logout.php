<?php
/**
 * Revoke the current user's session.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
// remove all session variables
session_unset();
// destroy the session
session_destroy();
// tell the client that they are logged out
header('Content-Type: application/json');
echo json_encode(['success' => true]);
