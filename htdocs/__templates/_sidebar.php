<?
$role = Session::get('role');
?>

<div class="d-flex flex-column flex-shrink-0 p-2 bg-body-tertiary sidebar-cus border-end">
    <? Session::loadTemplate('sidebar/_user', ['role' => $role]) ?>

    <!-- <hr> -->
    <ul class="nav nav-pills flex-column mb-auto ps-2">
        <!-- <li>
            <a href="/dashboard" class="nav-link link-body-emphasis">
                <svg class="bi pe-none me-2" width="16" height="16">
                    <use xlink:href="#home"></use>
                </svg>
                <p>Dashboard</p>
            </a>
        </li> -->

        <li>
            <a href="https://www.psnacet.edu.in/BE-CSE/CSE-About.php" class="nav-link link-body-emphasis">
                <svg class="bi pe-none me-2" width="16" height="16">
                    <use xlink:href="#home"></use>
                </svg>
                <p>Home</p>
            </a>
        </li>

        <!-- <?php // Session::loadTemplate("sidebar/_common");?> -->

        <?php Session::loadTemplate('/sidebar/_temp'); ?>

        <?php if ($role == "admin"): ?>
            <? Session::loadTemplate('sidebar/_admin') ?>
        <?php endif; ?>

        <?php if ($role == "faculty"): ?>
            <? Session::loadTemplate('sidebar/_faculty') ?>
        <?php endif; ?>

        <?php if ($role == "student"): ?>
            <? Session::loadTemplate('sidebar/_student') ?>
        <?php endif; ?>

    </ul>

    <? //Session::loadTemplate("sidebar/_bottomimg") ?>
</div>