<pre>
<?

include_once 'libs/load.php';

$listOfLinks = [
    [
        'name' => 'Enter Test Marks',
        'icon' => 'new-folder',
        'subLinks' => []
    ]
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

            $listOfLinks[0]['subLinks'][] = [
                'name' => $testName,
                'icon' => 'journal-plus',
                'href' => "/markentry?code={$encodedSubjectCode}&testname={$encodedTestName}&batch={$encodedBatch}&semester={$encodedSemester}&maxmark={$details['maxmark']}"
            ];
        }
    }
} else {
    $listOfLinks[] = [
        'name' => 'No tests found',
        'icon' => 'journal-plus',
        'href' => '#'
    ];
}

print_r($listOfLinks);


echo "<br><br>";

echo "Admin";

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
                'href' => '/assignrole'
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
    ]
];

print_r($listOfLinks);



?>

</pre>