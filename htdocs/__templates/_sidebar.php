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

                    <?php
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
                <?php endif; ?>

                <?php if ($role == "faculty"): ?>
                    <? Session::loadTemplate('sidebar/_faculty') ?>
                <?php endif; ?>

                <?php if ($role == "admin"): ?>
                   <? Session::loadTemplate('sidebar/_admin') ?>
                <?php endif; ?>

            </ul>

            <hr class="my-3">

            <? Session::loadTemplate('sidebar/_otheressentials') ?>
        </div>
    </div>
</div>