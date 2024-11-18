<?
$role = Session::get('role');
?>
<div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
    <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">Project CSE</h5>
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
                <!-- <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" href="#">
                        <svg class="bi">
                            <use xlink:href="#home-door" />
                        </svg>
                        Home
                    </a>
                </li> -->

            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-body-secondary text-uppercase">
                <span>Reports</span>
                <a class="link-secondary" href="#" aria-label="Add a new report">
                    <!-- <svg class="bi">
                        <use xlink:href="#plus-circle" />
                    </svg> -->
                </a>
            </h6>
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" href="#">
                        <svg class="bi">
                            <use xlink:href="#file-earmark-text" />
                        </svg>
                        Serial Test 1 (Coming Soon)
                    </a>
                </li>

                <?php if ($role == "faculty" || $role == "admin"): ?>
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
                                    foreach ($tests as $testName => $testCodes) {
                                        foreach ($testCodes as $testCode) {
                                            echo "<li class='nav-item'>
                                                    <a class='nav-link d-flex align-items-center gap-2' href='/markentry?code={$testCode}&testname={$testName}'>
                                                        <svg class='bi'>
                                                            <use xlink:href='#journal-plus' />
                                                        </svg>
                                                        {$testName} ({$testCode})
                                                    </a>
                                                  </li>";
                                        }
                                    }
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