
<?php

$listOfLinks = [
    [
        'name' => 'View Test Marks',
        'icon' => 'journal-plus',
        'subLinks' => []
    ],
    [
        'name' => 'Time Table',
        'icon' => 'calendar3',
        'href' => '/timetable'
    ],
    [
        'name' => 'Attendance',
        'icon' => 'calendar2-check',
        'subLinks' => [
            [
                'name' => 'Summary',
                'icon' => 'person-lines-fill',
                'href' => '/attendance?atye=' . base64_encode('sw')
            ],
            [
                'name' => 'Date Wise',
                'icon' => 'person-check-fill',
                'href' => '/attendance?atye=' . base64_encode('at')
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
                <div class='collapse' id='{$name}Collapse'>
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