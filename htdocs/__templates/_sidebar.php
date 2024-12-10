<?
$role = Session::get('role');
?>
<div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
    <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">Project CSE </h5>
            <h6> <span class="badge rounded-pill text-bg-info">Beta</span></h6>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" aria-current="page" href="/dashboard">
                        <svg class="bi">
                            <use xlink:href="#house-fill" />
                        </svg>
                        Dashboard
                    </a>
                </li>
            </ul>

            <ul class="nav flex-column mb-auto">

            <?php if ($role == "admin"): ?>
                <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-body-secondary text-uppercase">
                    <span>Reports</span>
                    <a class="link-secondary" href="#" aria-label="Add a new report">
                    </a>
                </h6>

                <?

                // Fetch tests from the database
                $tests = Test::getTests();
                ?>

                <?php foreach ($tests as $test): ?>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="/report?testid=<?= base64_encode((string)$test->_id) ?>&testname=<?= base64_encode($test->testname) ?>&dept=<?= base64_encode($test->department) ?>">
                            <svg class="bi">
                                <use xlink:href="#file-earmark-text" />
                            </svg>
                            <?= htmlspecialchars($test['testname']) ?>
                            <span class="text-muted ms-2">(<?= $test['status'] === 'active' ? 'Available' : 'Coming Soon' ?>)</span>
                        </a>
                    </li>
                <?php endforeach; ?>
                <?php endif;?>

                <?php if ($role == "faculty"): ?>
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

                <?php endif; ?>

                <?php if ($role == "admin"): ?>
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-body-secondary text-uppercase">
                        <span>Admin</span>
                        <a class="link-secondary" href="#" aria-label="Add a new report">

                        </a>
                    </h6>

                    <li class="nav-item">
                        <button class="nav-link d-flex align-items-center gap-2 btn-toggle" data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="false">
                            <svg class="bi">
                                <use xlink:href="#new-folder"></use>
                            </svg>
                            Create
                            <svg class="bi arrow-mov ">
                                <use xlink:href="#plus"></use>
                            </svg>
                        </button>
                        <div class="collapse" id="dashboard-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center gap-2" href="/createuser">
                                        <svg class="bi">
                                            <use xlink:href="#person-fill" />
                                        </svg>
                                        User
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center gap-2" href="/createsubject">
                                        <svg class="bi">
                                            <use xlink:href="#journal-plus" />
                                        </svg>
                                        Subject
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center gap-2" href="/createtest">
                                        <svg class="bi">
                                            <use xlink:href="#journal-plus" />
                                        </svg>
                                        Test
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="/enrollsubjects">
                            <svg class="bi">
                                <use xlink:href="#file-earmark-text" />
                            </svg>
                            Enroll Students
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="/assignfaculty">
                            <svg class="bi">
                                <use xlink:href="#file-earmark-text" />
                            </svg>
                            Assign Faculty
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="/assignrole">
                            <svg class="bi">
                                <use xlink:href="#file-earmark-text" />
                            </svg>
                            Assign Role
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="/managerole">
                            <svg class="bi">
                                <use xlink:href="#file-earmark-text" />
                            </svg>
                            Manage Role
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="/managepermission">
                            <svg class="bi">
                                <use xlink:href="#file-earmark-text" />
                            </svg>
                            Manage Permission
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

            <hr class="my-3">

            <ul class="nav flex-column mb-auto">
                <!-- <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" href="#">
                        <svg class="bi">
                            <use xlink:href="#gear-wide-connected" />
                        </svg>
                        Settings
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" id="signOutBtn" href="?logout">
                        <svg class="bi">
                            <use xlink:href="#door-closed" />
                        </svg>
                        Sign out
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>