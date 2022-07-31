<?php
// delete this file after use
// THIS FILE IS NOT TO BE ASSESSED. IT IS A TEMPORARY FILE.

include './API/Helpers/initDatabaseConnection.php';
include './API/Helpers/addActivityItem.php';
$db = getConnection();

// generate array of random mac addresses
$mac_addresses = array();
for ($i = 0; $i < 10000; $i++) {
    $mac_addresses[] = sprintf('%02X:%02X:%02X:%02X:%02X:%02X', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
}

// generate array of random ip addresses
$ip_addresses = array();
for ($i = 0; $i < 10000; $i++) {
    $ip_addresses[] = sprintf('10.132.%d.%d', mt_rand(6, 12), mt_rand(2, 255));
}

// generate an array of random computer manufacturers
$manufacturers = array('Apple', 'Dell', 'HP', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP', 'IBM', 'Intel', 'Lenovo', 'Microsoft', 'Samsung', 'Sony', 'Acer', 'Asus', 'Compaq', 'D-Link', 'Gateway', 'Google', 'Hewlett-Packard', 'HP');

//generate an array of random device names
$device_names = array();
for ($i = 0; $i < 10000; $i++) {
    $randomLetter = chr(mt_rand(65, 90));
    $prefix = $manufacturers[mt_rand(0, count($manufacturers) - 1)];
    $device_names[] = strtoupper(substr($prefix, 0, 2) . '-' . mt_rand(1, 9999) . $randomLetter);
}

// generate an array of random device models
$device_models = array();
for ($i = 0; $i < 10000; $i++) {
    $prefix = $manufacturers[mt_rand(0, count($manufacturers) - 1)];
    $device_models[] = $prefix . ' ' . mt_rand(1, 9999);
}

// generate an array of random serial numbers
$serial_numbers = array();
for ($i = 0; $i < 10000; $i++) {
    $serial_numbers[] = sprintf('%02X%02X%02X%02X%02X', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
}

$brief_descriptions = array('Reserved ip address during campus renovation (06/21)', 'Installed as part of FU4324 fund', 'Needs repair', 'Needs to be relocated to new campus', 'Out of warranty', 'On Lease', 'To be replaced', 'Firmware update pending', 'Firmware update failing', '');

$connection_types = array('wired', 'wireless');

// get all entities from entities table
$sql = "SELECT * FROM entities";
$result = $db->query($sql);
$entities = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $entities[] = $row;
    }
}

// get all areas from areas table
$sql = "SELECT * FROM areas";
$result = $db->query($sql);
$areas = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $areas[] = $row;
    }
}
// filter by are type building
$buildings = array();
foreach ($areas as $area) {
    if ($area['type'] == 'room') {
        $buildings[] = $area;
    }
}

// for each entity, update stuff
foreach ($entities as $entity) {
    $entity_types = array(1,2,6,18,20,21,22);
    $entity_type = $entity_types[array_rand($entity_types)];
    $mac_address = $mac_addresses[array_rand($mac_addresses)];
    $ip_address = $ip_addresses[array_rand($ip_addresses)];
    $manufacturer = $manufacturers[array_rand($manufacturers)];
    $serial_number = $serial_numbers[array_rand($serial_numbers)];
    $area_id = $buildings[array_rand($buildings)]['id'];
    $device_name = $device_names[array_rand($device_names)];
    $device_model = $device_models[array_rand($device_models)];
    $connection_type = $connection_types[array_rand($connection_types)];
    $brief_description = $brief_descriptions[array_rand($brief_descriptions)];

    $sql = "UPDATE entities SET brief_notes = '$brief_description', model = '$device_model', name = '$device_name', mac_address='$mac_address', ip_address='$ip_address', manufacturer='$manufacturer', serial_number='$serial_number', area_id = '$area_id', type_id = '$entity_type' WHERE id='$entity[id]'";
    $db->query($sql);

    $statuses = array('online', 'offline');
    //repeat 30 times
//    for ($i = 0; $i < 30; $i++) {
//        $status = $statuses[array_rand($statuses)];
//        $timestamp = time() - mt_rand(0, 86400 * 365);
//        addActivityItem($db, 'entity', $entity['id'], ':entity went :status', [
//            'entity' => [
//                'text' => $device_name,
//                'href' => '/devices/' . $entity['id'],
//            ],
//            'status' => [
//                'text' => $status,
//                'color' => $status == 'online' ? 'green' : 'red',
//            ],
//        ], $timestamp);
//    }
    // update fields
    $fieldNames = array('brief_notes', 'model', 'name', 'mac_address', 'ip_address', 'manufacturer', 'serial_number', 'area_id', 'type_id');
//    foreach ($fieldNames as $fieldName) {
//        $timestamp = time() - mt_rand(0, 86400 * 165);
//        addActivityItem($db, 'entity', $entity['id'],   ":user :verb :entity -> :field",
//            [
//                'user' => [
//                    'text' => $_SESSION['username'],
//                    'href' => '/SAT_BRH/settings'
//                ],
//                'verb' => [
//                    'text' => 'updated',
//                    'color' => '#673ab7'
//                ],
//                'entity' => [
//                    'text' => $entity['name'] != null && $entity['name'] != '' ? $entity['name'] : $entity['ip_address'],
//                    'href' => '/SAT_BRH/devices/' . $entity['id']
//                ],
//                'field' => [
//                    'text' => $fieldName,
//                    'color' => '#607d8b'
//                ],
//                'newValue' => [
//                    'text' => '',
//                    'color' => '#607d8b'
//                ]
//            ], $timestamp);
//    }
}

header('Content-Type: application/json');
echo json_encode($entities);



// for each building, generate fake rooms
//foreach ($buildings as $building) {
//    $rooms = array();
//    for ($i = 0; $i < mt_rand(10, 30); $i++) {
//        $rooms[] = array(
//            'name' => str_replace('Building ', '', $building['name']) . $i,
//            'description' => 'Classroom',
//            'parent_id' => $building['id'],
//            'type' => 'room'
//        );
//    }
//    foreach ($rooms as $room) {
//        $sql = "INSERT INTO areas (name, description, parent_id, type) VALUES ('$room[name]', '$room[description]', '$room[parent_id]', '$room[type]')";
//        $db->query($sql);
//    }
//
//}

