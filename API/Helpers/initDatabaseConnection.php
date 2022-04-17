<?php
/**
 * @return mysqli|void
 */
function getConnection() {
    $servername = "127.0.0.1";
    $username = "vsvDemo";
    $password = "B@nanas_r_BetterThanGrapes834";
    $dbname = "SAT_BRH";

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(array(
                'error' => array(
                    'message' => 'Server Error',
                    'code' => 500,
                    'details' => $conn->connect_error
                )
            ));
            exit();
        } else {
            return $conn;
        }
    } catch (Exception $e) {
        // send back server error
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(array(
            'error' => array(
                'message' => 'Server Error',
                'code' => 500,
                'details' => $e->getMessage()
            )
        ));
        exit();
    }
}
