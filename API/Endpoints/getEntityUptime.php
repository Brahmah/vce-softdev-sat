<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
// wait for 1 second
sleep(1);
?>

{"type":"line","data":{"labels":["10 am","11 am","12 pm","1 pm","2 pm","3 pm","4 pm","5 pm","6 pm","7 pm","8 pm","9 pm"],"datasets":[{"label":"Uptime","data":[100,100,100,100,100,100,100,100,100,100,100,100],"borderColor":"green","backgroundColor":"green"}]},"options":{"scales":{"y":{"max":105,"min":0,"ticks":{"beginAtZero":true}}}}}