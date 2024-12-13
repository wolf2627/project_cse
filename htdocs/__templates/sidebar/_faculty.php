<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-body-secondary text-uppercase">
    <span>Faculty</span>
</h6>

<li class="nav-item">
    <button class="nav-link d-flex align-items-center gap-2 btn-toggle" data-bs-toggle="collapse" data-bs-target="#mark-enter-collapse" aria-expanded="false">
        <svg class="bi">
            <use xlink:href="#new-folder"></use>
        </svg>
        Enter Test Marks
        <svg class="bi arrow-mov ">
            <use xlink:href="#plus"></use>
        </svg>
    </button>

    
    <div class="collapse" id="mark-enter-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
            <?php
            $faculty = new Faculty();
            $tests = $faculty->getFacultyAssignedTests();

            if (!empty($tests)) {
                foreach ($tests as $testName => $details) {
                    $encodedTestName = base64_encode($testName);
                    $department = $details['department'];

                    foreach ($details['subjects'] as $subjectCode) {
                        $encodedSubjectCode = base64_encode($subjectCode);
                        $encodedBatch = base64_encode(implode(", ", $details['batches']));
                        $encodedSemester = base64_encode(implode(", ", $details['semesters']));

                        echo "<li class='nav-item'>
                                <a class='nav-link d-flex align-items-center gap-2' 
                                    href='/markentry?code={$encodedSubjectCode}&testname={$encodedTestName}&batch={$encodedBatch}&semester={$encodedSemester}&maxmark={$details['maxmark']}'>
                                    <svg class='bi'>
                                        <use xlink:href='#journal-plus'></use>
                                    </svg>
                                    {$testName}
                                    <small class='text-muted'>
                                        {$subjectCode} | 
                                        Dept : {$department} |
                                        Sem: " . implode(", ", $details['semesters']) . "
                                    </small>
                                </a>
                                </li>";
                    }
                }
            } else {
                echo "<li class='nav-item'>
                        <a class='nav-link d-flex align-items-center gap-2' href='#'>
                            <svg class='bi'>
                                <use xlink:href='#journal-plus'></use>
                            </svg>
                            No tests found
                        </a>
                        </li>";
            }
            ?>
        </ul>
    </div>
</li>