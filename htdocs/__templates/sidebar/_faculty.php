<h6 class="sidebar-heading justify-content-between align-items-center px-3 text-muted">
    <span>Faculty</span>
</h6>

<?php

$listOfLinks = [
    [
        'name' => 'Time Table',
        'icon' => 'calendar3',
        'href' => '/timetable'
    ],
    [
        'name' => 'Enter Test Marks',
        'icon' => 'new-folder',
        'subLinks' => []
    ],
    [
        'name' => 'Mark Attendance',
        'icon' => 'person-check-fill',
        'href' => '/markattendance'
    ],
    [
        'name' => 'View Attendance',
        'icon' => 'person-lines-fill',
        'href' => '/viewattendance'
    ],
    [
        'name' => 'View Ward Students',
        'icon' => 'people-fill',
        'href' => '/wardstudents'
    ],
];


$fauculty = new Faculty();
$tests = $fauculty->getFacultyAssignedTests();


if (!empty($tests)) {
    foreach ($tests as $testName => $details) {
        $encodedTestName = base64_encode($testName);
        $department = $details['department'];

        foreach ($details['subjects'] as $subjectCode) {
            $encodedSubjectCode = base64_encode($subjectCode);
            $encodedBatch = base64_encode(implode(", ", $details['batches']));
            $encodedSemester = base64_encode(implode(", ", $details['semesters']));

            $listOfLinks[1]['subLinks'][] = [
                'name' => $testName,
                'icon' => 'journal-plus',
                'href' => "/markentry?code={$encodedSubjectCode}&testname={$encodedTestName}&batch={$encodedBatch}&semester={$encodedSemester}&maxmark={$details['maxmark']}"
            ];
        }
    }
} else {
    $listOfLinks[1]['subLinks'][] = [
        'name' => 'No tests found',
        'icon' => 'journal-plus',
        'href' => '#'
    ];
}


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