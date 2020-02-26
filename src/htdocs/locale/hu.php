<?php
$jsLocale = array();
$locale = array();

$locale['_widgets'] = [
    0 => [
        'label' => 'Kíváló',
        'icon' => 'fa-smile-o',
        'recommendations' => [
            [
                'icon' => 'icon-spacer',
                'color' => 'green',
                'label' => 'Ajánlott',
                'description' => 'szabadtéri séták'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'green',
                'label' => 'Ajánlott',
                'description' => 'szabadtéri fizikai tevékenység'
            ],
            [
                'icon' => 'icon-tree',
                'color' => 'green',
                'label' => 'Ajánlott',
                'description' => 'szabadtéri szabadidős tevékenység'
            ],
            [
                'icon' => 'icon-elderly',
                'color' => 'green',
                'label' => 'Ajánlott',
                'description' => 'szabadtéri szabadidős tevékenység (terhes nők, gyermekek, légúti betegségben szenvedők, szépkorúak)'
            ],
        ]
    ],
    1 => [
        'label' => 'Jó',
        'icon' => 'fa-meh-o',
        'recommendations' => [
            [
                'icon' => 'icon-spacer',
                'color' => 'green',
                'label' => 'Ajánlott',
                'description' => 'szabadtéri séták'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'green',
                'label' => 'Ajánlott',
                'description' => 'szabadtéri fizikai tevékenység'
            ],
            [
                'icon' => 'icon-tree',
                'color' => 'green',
                'label' => 'Ajánlott',
                'description' => 'szabadtéri szabadidős tevékenység'
            ],
            [
                'icon' => 'icon-elderly',
                'color' => 'orange',
                'label' => 'Nem ajánlott',
                'description' => 'szabadtéri szabadidős tevékenység (terhes nők, gyermekek, légúti betegségben szenvedők, szépkorúak)'
            ],
        ]
    ],
    2 => [
        'label' => 'Megfelelő',
        'icon' => 'fa-meh-o',
        'recommendations' => [
            [
                'icon' => 'icon-spacer',
                'color' => 'green',
                'label' => 'Elfogadható',
                'description' => 'szabadtéri séták'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'green',
                'label' => 'Elfogadható',
                'description' => 'szabadtéri szabadidős tevékenység'
            ],
            [
                'icon' => 'icon-tree',
                'color' => 'orange',
                'label' => 'Nem ajánlott',
                'description' => 'hoszabb szabadtéri tartózkodás'
            ],
            [
                'icon' => 'icon-elderly',
                'color' => 'orange',
                'label' => 'Nem ajánlott',
                'description' => 'hoszabb szabadtéri tartózkodás (terhes nők, gyermekek, légúti betegségben szenvedők, szépkorúak)'
            ],
        ]
    ],
    3 => [
        'label' => 'Egészségtelen',
        'icon' => 'fa-frown-o',
        'recommendations' => [
            [
                'icon' => 'icon-home',
                'color' => 'green',
                'label' => 'Ajánlott',
                'description' => 'Lakásban maradni (terhes nók, gyermekek, szépkorúak, szív és elderly, szív és légúti betegségben szenvedők)'
            ],
            [
                'icon' => 'icon-spacer',
                'color' => 'orange',
                'label' => 'Nem ajánlott',
                'description' => 'szabadtéri séták'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'orange',
                'label' => 'Nem ajánlott',
                'description' => 'szabadtérben tartózkodás'
            ],
            [
                'icon' => 'icon-car',
                'color' => 'orange',
                'label' => 'Halassza el',
                'description' => 'gépjárművel közlekedés'
            ],
        ]
    ],
    4 => [
        'label' => 'Veszélyes',
        'icon' => 'fa-frown-o',
        'recommendations' => [
            [
                'icon' => 'icon-home',
                'color' => 'green',
                'label' => 'Ajánlott',
                'description' => 'lakásban maradni'
            ],
            [
                'icon' => 'icon-spacer',
                'color' => 'red',
                'label' => 'Halassza el',
                'description' => 'szabadtéri séták'
            ],
            [
                'icon' => 'icon-rower',
                'color' => 'red',
                'label' => 'Halaszza el',
                'description' => 'szabadtéri szabadidős tevékenység'
            ],
            [
                'icon' => 'icon-car',
                'color' => 'red',
                'label' => 'Halassza el',
                'description' => 'gépjárművel közlekedés'
            ],
        ]
    ]
];

?>