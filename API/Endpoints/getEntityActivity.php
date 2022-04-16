<?php

use Models\Entity;

include_once '../Models/Entity.php';
header('Access-Control-Allow-Origin: *');

// init entity
$entity = new Entity($_GET['entityId']);
// send to client
header('Content-Type: application/json');
sleep(1);


$result = [
    'list' => [
        [
        'title' => 'Jeff Bezos',
        'description' => 'Updated Device Details',
        'timestamp' => '2018-01-01 00:00:00',
        'id' => '1',
        'context' => 'entity',
        'parentNodeId' => '1',
        'visibleToReadRole' => true,
        'visibleToWriteRole' => true,
        'detailHtml' => '<p>This is a test</p>',
            'image' => 'https://prabook.com/web/show-photo-icon.jpg?id=1918927&width=220&cache=true',
            'isRecent' => true,
            'relativeTime' => '1 day ago',
    ],
        [
        'title' => 'Jeff Bezos',
        'description' => 'Updated Device Details',
        'timestamp' => '2018-01-01 00:00:00',
        'id' => '2',
        'context' => 'entity',
        'parentNodeId' => '1',
        'visibleToReadRole' => true,
        'visibleToWriteRole' => true,
        'detailHtml' => '<p>This is a test</p>',
            'image' => 'https://prabook.com/web/show-photo-icon.jpg?id=1918927&width=220&cache=true',
            'isRecent' => false,
            'relativeTime' => '1 day ago',
        ],
        [
        'title' => 'Jeff Bezos',
        'description' => 'Updated Device Details',
        'timestamp' => '2018-01-01 00:00:00',
        'id' => '3',
        'context' => 'entity',
        'parentNodeId' => '1',
        'visibleToReadRole' => true,
        'visibleToWriteRole' => true,
        'detailHtml' => '<p>This is a test</p>',
            'image' => 'https://prabook.com/web/show-photo-icon.jpg?id=1918927&width=220&cache=true',
            'isRecent' => false,
            'relativeTime' => '1 day ago',
        ]
    ]
];

echo json_encode($result);
