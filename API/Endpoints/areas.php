<?php
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Origin: *');

$areas = [
    'list' => [
        [
            'id' => 1,
            'name' => 'Front Office',
            'description' => 'D2.1',
            'backgroundColor' => '#ff0000',
        ],
        [
            'id' => 2,
            'name' => 'Back Office',
            'description' => 'D2.2',
            'backgroundColor' => '#00ff00',
        ],
        [
            'id' => 3,
            'name' => 'IT',
            'description' => 'D2.3',
            'backgroundColor' => '#0000ff',
        ],
        [
            'id' => 4,
            'name' => 'Sales',
            'description' => 'D2.4',
            'backgroundColor' => '#ffff00',
        ],
        [
            'id' => 5,
            'name' => 'Marketing',
            'description' => 'D2.5',
            'backgroundColor' => '#ff00ff',
        ],
        [
            'id' => 6,
            'name' => 'Finance',
            'description' => 'D2.6',
            'backgroundColor' => '#00ffff',
        ],
        [
            'id' => 7,
            'name' => 'Human Resources',
            'description' => 'D2.7',
            'backgroundColor' => '#00ff00',
        ],
        [
            'id' => 8,
            'name' => 'Customer Service',
            'description' => 'D2.8',
            'backgroundColor' => '#000000',
        ],
        [
            'id' => 9,
            'name' => 'Research',
            'description' => 'D2.9',
            'backgroundColor' => '#ffff00',
        ],
        [
            'id' => 10,
            'name' => 'Customer Relations',
            'description' => 'D2.10',
            'backgroundColor' => '#00ffff',
        ],
        [
            'id' => 11,
            'name' => 'Production',
            'description' => 'D2.11',
            'backgroundColor' => '#ff0000',
        ],
        [
            'id' => 12,
            'name' => 'Quality Control',
            'description' => 'D2.12',
            'backgroundColor' => '#0000ff',
        ],
        [
            'id' => 13,
            'name' => 'Research and Development',
            'description' => 'D2.13',
            'backgroundColor' => '#ffff00',
        ],
        [
            'id' => 14,
            'name' => 'Accounting',
            'description' => 'D2.14',
            'backgroundColor' => '#00ff00',
        ],
        [
            'id' => 15,
            'name' => 'Customer Service',
            'description' => 'D2.15',
            'backgroundColor' => '#000000',
        ],
        [
            'id' => 16,
            'name' => 'Human Resources',
            'description' => 'D2.16',
            'backgroundColor' => '#00ff00',
        ],
        [
            'id' => 17,
            'name' => 'Research and Development',
            'description' => 'D2.17',
            'backgroundColor' => '#ffff00',
        ],
        [
            'id' => 18,
            'name' => 'Sales',
            'description' => 'D2.18',
            'backgroundColor' => '#00ffff',
        ],
        [
            'id' => 19,
            'name' => 'Production',
            'description' => 'D2.19',
            'backgroundColor' => '#ff0000',
        ],
        [
            'id' => 20,
            'name' => 'Quality Control',
            'description' => 'D2.20',
            'backgroundColor' => '#0000ff',
        ],
        [
            'id' => 21,
            'name' => 'Research and Development',
            'description' => 'D2.21',
            'backgroundColor' => '#ffff00',
        ],
        [
            'id' => 22,
            'name' => 'Accounting',
            'description' => 'D2.22',
            'backgroundColor' => '#00ff00',
        ],
        [
            'id' => 23,
            'name' => 'Customer Service',
            'description' => 'D2.23',
            'backgroundColor' => '#000000',
        ],
        [
            'id' => 24,
            'name' => 'Human Resources',
            'description' => 'D2.24',
            'backgroundColor' => '#00ff00',
        ],
        [
            'id' => 25,
            'name' => 'Research and Development',
            'description' => 'D2.25',
            'backgroundColor' => '#ffff00',
        ]
    ]
];

// add _SearchTerms to each area
$areas['list'] = array_map(function ($area) {
    $area['_SearchTerms'] = strtolower($area['name'] . ' ' . $area['description']);
    return $area;
}, $areas['list']);

echo json_encode($areas);


