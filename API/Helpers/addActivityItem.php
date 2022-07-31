<?php
/**
 * This file provides the functionality to add a new activity item to the database.
 *
 * @author Bashir Rahmah <brahmah90@gmail.com>
 * @copyright Bashir Rahmah 2022
 *
 */
require_once './API/Models/ActivityItem.php';

/**
 * @param $db MySQLi database connection
 * @param $context string context of the item. Used in conjunction with the itemId to determine the location of activity to insert.
 * @param string $itemId int id of the item to insert activity for
 * @param string $body string body of the activity ':person did something to :entity'
 * @param array $param array of parameters for body. 'person => ['text' => 'John'], 'entity => ['text' => 'DP-AC1']'
 * @return void
 * @throws Exception
 */
function addActivityItem(mysqli $db, string $context, string $itemId, string $body, array $param)
{
    $date = date("Y-m-d H:i:s");
    $timestamp = time();
    $activityItemQueryPrepare = $db->prepare("insert into activity (`context`, `item_id`, `body`, `timestamp`, `date`, `param`) values (?, ?, ?, ?, ?, ?);");
    $encodedParam = json_encode($param);
    $activityItemQueryPrepare->bind_param("sissss", $context, $itemId, $body, $timestamp, $date, $encodedParam);
    $activityItemQuerySuccess = $activityItemQueryPrepare->execute();
    if (!$activityItemQuerySuccess) {
        throw new Exception("Failed to add activity item");
    }
}