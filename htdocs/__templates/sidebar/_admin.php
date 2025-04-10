<h6 class="sidebar-heading justify-content-between align-items-center px-3 text-muted">
    <span>Admin</span>
</h6>


<?php

$listOfLinks = [
    [
        'name' => 'Create',
        'icon' => 'new-folder',
        'subLinks' => [
            [
                'name' => 'User',
                'icon' => 'person-fill',
                'href' => '/createuser'
            ],
            [
                'name' => 'Subject',
                'icon' => 'journal-plus',
                'href' => '/createsubject'
            ],
            [
                'name' => 'Test',
                'icon' => 'journal-plus',
                'href' => '/createtest'
            ]
        ]
    ],
    [
        'name' => 'Assign Faculty',
        'icon' => 'bi-person-rolodex',
        'href' => '/assignfaculty'
    ],
    [
        'name' => 'Enroll Students',
        'icon' => 'file-person-fill',
        'href' => '/enrollsubjects'
    ],
    [
        'name' => 'Role',
        'icon' => 'new-folder',
        'subLinks' => [
            [
                'name' => 'Assign Role',
                'icon' => 'file-earmark-text',
                'href' => '/user-role-manage'
            ],
            [
                'name' => 'Manage Role',
                'icon' => 'file-earmark-text',
                'href' => '/managerole'
            ],
        ]
    ],
    [
        'name' => 'Permission',
        'icon' => 'new-folder',
        'subLinks' => [
            [
                'name' => 'manage',
                'icon' => 'file-earmark-text',
                'href' => '/managepermission'
            ],
        ]
    ],
    [
        'name' => 'Grant Permission',
        'icon' => 'file-earmark-text',
        'href' => '/role-permission-manage'
    ],
    [
        'name' => 'Time Table',
        'icon' => 'calendar3',
        'subLinks' => [
            [
                'name' => 'Create',
                'icon' => 'calendar2-plus',
                'href' => '/createtimetable'
            ],
            [
                'name' => 'Manage',
                'icon' => 'calendar-minus',
                'href' => '/managetimetable'
            ],
        ]
    ],
    [
        'name' => 'Year Incharge',
        'icon' => 'new-folder',
        'subLinks' => [
            [
                'name' => 'Assign',
                'icon' => 'file-earmark-text',
                'href' => '/assignyearincharge'
            ],
            [
                'name' => 'Manage',
                'icon' => 'file-earmark-text',
                'href' => '/manageyearincharge'
            ],
        ]
    ],
    [
        'name' => 'Tutor',
        'icon' => 'new-folder',
        'subLinks' => [
            [
                'name' => 'Assign',
                'icon' => 'file-earmark-text',
                'href' => '/assigntutor'
            ],
            [
                'name' => 'Manage',
                'icon' => 'file-earmark-text',
                'href' => '/managetutor'
            ],
        ]
    ],
    [
        'name' => 'Department',
        'icon' => 'new-folder',
        'subLinks' => [
            [
                'name' => 'project based Learning',
                'icon' => 'journal-plus',
                'href' => '/projectbasedlearning'
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