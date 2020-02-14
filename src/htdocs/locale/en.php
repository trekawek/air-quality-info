<?php
$jsLocale = array();
$locale = array();

$locale['_widgets'] = [
    0 => [
        'label' => 'Okay',
        'icon' => 'fa-smile-o',
        'recommendations' => [
            [
                'icon' => 'icon-spacer',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'walks outside'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'physical activity outside'
            ],
            [
                'icon' => 'icon-tree',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'spending time outdoors'
            ],
            [
                'icon' => 'icon-elderly',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'spending time outdoors (pregnant, asthmatic, elderly)'
            ],
        ]
    ],
    1 => [
        'label' => 'Moderate',
        'icon' => 'fa-meh-o',
        'recommendations' => [
            [
                'icon' => 'icon-spacer',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'walks outside'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'physical activity outside'
            ],
            [
                'icon' => 'icon-tree',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'spending time outdoors'
            ],
            [
                'icon' => 'icon-elderly',
                'color' => 'orange',
                'label' => 'Not recommended',
                'description' => 'spending time outdoors (pregnant, asthmatic, elderly)'
            ],
        ]
    ],
    2 => [
        'label' => 'Mediocre',
        'icon' => 'fa-meh-o',
        'recommendations' => [
            [
                'icon' => 'icon-spacer',
                'color' => 'green',
                'label' => 'Acceptable',
                'description' => 'walks outside'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'green',
                'label' => 'Acceptable',
                'description' => 'physical activity outside'
            ],
            [
                'icon' => 'icon-tree',
                'color' => 'orange',
                'label' => 'Not recommended',
                'description' => 'prolonged exposure in the open air'
            ],
            [
                'icon' => 'icon-elderly',
                'color' => 'orange',
                'label' => 'Not recommended',
                'description' => 'prolonged exposure in the open air (pregnant, asthmatic, elderly)'
            ],
        ]
    ],
    3 => [
        'label' => 'Unfavorable',
        'icon' => 'fa-frown-o',
        'recommendations' => [
            [
                'icon' => 'icon-home',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'Staying at home (elderly, pregnant women, people with heart and respiratory diseases)'
            ],
            [
                'icon' => 'icon-spacer',
                'color' => 'orange',
                'label' => 'Not recommended',
                'description' => 'walks outside'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'orange',
                'label' => 'Not recommended',
                'description' => 'physical activity outside'
            ],
            [
                'icon' => 'icon-car',
                'color' => 'orange',
                'label' => 'Limit',
                'description' => 'driving a car'
            ],
        ]
    ],
    4 => [
        'label' => 'Bad',
        'icon' => 'fa-frown-o',
        'recommendations' => [
            [
                'icon' => 'icon-home',
                'color' => 'green',
                'label' => 'Recommended',
                'description' => 'staying at home'
            ],
            [
                'icon' => 'icon-spacer',
                'color' => 'red',
                'label' => 'Postpone',
                'description' => 'walks outside'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'red',
                'label' => 'Postpone',
                'description' => 'physical activity outside'
            ],
            [
                'icon' => 'icon-car',
                'color' => 'red',
                'label' => 'Postpone',
                'description' => 'driving a car'
            ],
        ]
    ]
];

?>