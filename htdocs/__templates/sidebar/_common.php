
<?php

$listOfLinks = [
    [
        'name' => 'Department',
        'icon' => 'new-folder',
        'subLinks' => [
            // [
            //     'name' => 'project based Learning',
            //     'icon' => 'journal-plus',
            //     'href' => '/projectbasedlearning'
            // ],[
            //     'name' => 'Innovations by Faculty',
            //     'icon' => 'journal-plus',
            //     'href' => '/innovationbyfaculty'
            // ],
            [
                'name' => 'E-Studio',
                'icon' => 'journal-plus',
                'href' => '/e-studio'
            ],
            // [
            //     'name' => 'Internships',
            //     'icon' => 'journal-plus',
            //     'href' => '/internships'
            // ],
            // [
            //     'name' => 'IndustrialVisit',
            //     'icon' => 'journal-plus',
            //     'href' => '/industrialvisit'
            // ],
            // [
            //     'name' => 'Faculty-Participation',
            //     'icon' => 'journal-plus',
            //     'href' => '/facultyparticipation'
            // ],
            // [
            //     'name' => 'Faculty-Publication',
            //     'icon' => 'journal-plus',
            //     'href' => '/facultypublication'
            // ]
        ],

    ],
    [
        'name' => 'Instructional Methods',
        'icon' => 'new-folder',
        'subLinks' => [
            [
                'name' => 'Activity Based Learning',
                'icon' => 'journal-plus',
                'href' => '/activitybasedlearning'
            ],
            [
                'name' => 'Flipped Learning',
                'icon' => 'journal-plus',
                'href' => '/flippedlearning'

            ],
            [
                'name' => 'Experimental Learning',
                'icon' => 'journal-plus',
                'href' => '/experimentallearning'

            ],
            [
                'name' => 'Collabrative Learning',
                'icon' => 'journal-plus',
                'href' => '/collabrativelearning'

            ],
            [
                'name' => 'Competency Based Education',
                'icon' => 'journal-plus',
                'href' => '/competencybasededucation'

            ]
        ]
    ]
];


foreach ($listOfLinks as $link) {
    $subLinks = $link['subLinks'] ?? [];
    $href = $link['href'] ?? '#';
    $icon = $link['icon'] ?? '';
    $name = str_replace(' ', '-', $link['name'] ?? '');

    if (!empty($subLinks)) {
        echo "<li>
                <a href='#{$name}Collapse' class='nav-link link-body-emphasis' data-bs-toggle='collapse' role='button' aria-expanded='false' aria-controls='{$name}Collapse'>
                    <svg class='bi pe-none me-2' width='16' height='16'>
                        <use xlink:href='#{$icon}'></use>   
                    </svg>
                    <p>{$name}</p>
                </a>
                <div class='collapse' id='{$name}Collapse' style='background-color:rgb(116, 116, 116, 0.1)'>
                    <ul class='list-unstyled ps-0'>";
        foreach ($subLinks as $subLink) {
            $subHref = $subLink['href'] ?? '#';
            $subIcon = $subLink['icon'] ?? '';
            $subName = $subLink['name'] ?? '';

            echo "<li>
                    <a href='{$subHref}' class='nav-link link-body-emphasis'>
                        <svg class='bi pe-none me-2' width='16' height='16'>
                            <use xlink:href='#{$subIcon}'></use>
                        </svg>
                        <p>{$subName}</p>
                    </a>
                </li>";
        }
        echo "</ul>
            </div>
        </li>";
    } else {
        echo "<li>
                <a href='{$href}' class='nav-link link-body-emphasis'>
                    <svg class='bi pe-none me-2' width='16' height='16'>
                        <use xlink:href='#{$icon}'></use>
                    </svg>
                    <p>{$name}</p>
                </a>
            </li>";
    }
}
?>