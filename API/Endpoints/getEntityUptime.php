<?php
// DISCLAIMER: This file is not part of the assignment. Merely a placeholder for the code snippets.
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

// should store row in database like this:
// ENTITY_ID, ENTITY_UPTIME_AVERAGE, ENTITY_UPTIME_CHECKPOINTS_COUNT, ENTITY_UPTIME_MAX, ENTITY_UPTIME_MIN
// Where checkpoints are the number of times the entity has been updated used to calculate the average uptime.
// So how do we calculate the average uptime?
// -------- (10+40+30+20+50) / 5 = 30, we need the count of all elements and their sum, right?
// -------- If we store the count of added elements and last value, we can add the new uptime status to the
//--------- sum and divide it by the newly incremented count.


/* URI Parameters */
/** @var string $type URI Parameter */

if ($type === 'Daily') {
    ?>
    {"type":"line","data":{"labels":["Wednesday, Jun 29","Thursday, Jun 30","Friday, Jul 1","Saturday, Jul 2","Sunday, Jul 3","Monday, Jul 4","Tuesday, Jul 5","Wednesday, Jul 6","Thursday, Jul 7","Friday, Jul 8","Saturday, Jul 9","Sunday, Jul 10","Monday, Jul 11","Tuesday, Jul 12"],"datasets":[{"label":"Uptime","data":[100,90,90,100,100,40,100,80,90,100,100,87,100,85],"borderColor":"green","backgroundColor":"green"}]},"options":{"scales":{"y":{"max":105,"min":0,"ticks":{"beginAtZero":true}}}}}    <?php
}
?>

<?php
if ($type === 'Hourly') {
    ?>
    {"type":"line","data":{"labels":["10 am","11 am","12 pm","1 pm","2 pm","3 pm","4 pm","5 pm","6 pm","7 pm","8 pm","9 pm"],"datasets":[{"label":"Uptime","data":[100,70,100,100,50,70,90,100,100,100,90,100],"borderColor":"green","backgroundColor":"green"}]},"options":{"scales":{"y":{"max":105,"min":0,"ticks":{"beginAtZero":true}}}}}
    <?php
}
?>

