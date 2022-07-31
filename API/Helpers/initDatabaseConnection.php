<?php
/**
 * Initialize the database connection.
 *
 * This file provides the functionality to initialize the database connection.
 * The function will return a MySQLi database connection or will throw an error
 * if the connection fails and exit the program to prevent calls being made to the database
 * without a valid connection.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */

/**
 * @return mysqli|void
 */
function getConnection()
{
    $servername = "127.0.0.1";
    $username = "vsvDemo";
    $password = "B@nanas_r_BetterThanGrapes834";
    $dbname = "SAT_BRH";

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            header('HTTP/1.1 500 Internal Server Error');
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(array(
                'success' => false,
                'status' => 'Server Error',
                'message' => $conn->connect_error
            ));
            exit();
        } else {
            return $conn;
        }
    } catch (Exception $e) {
        // send back server error
        header('HTTP/1.1 500 Internal Server Error');
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(array(
            'success' => false,
            'status' => 'Server Error',
            'message' => $e->getMessage()
        ));
        exit();
    }
}
