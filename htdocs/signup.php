<?php
include 'libs/load.php';
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<!-- load head -->

<? Session::loadTemplate('_head'); ?>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <!-- load mode toggle -->
    <? Session::loadTemplate('_modetoggle'); ?>

    <!-- load svg for using icons -->
    <? Session::loadTemplate('_svg'); ?>



    <? Session::loadTemplate('_signup'); ?>
    <!-- load dashboard -->

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.js" integrity="sha384-eI7PSr3L1XLISH8JdDII5YN/njoSsxfbrkCTnJrzXt+ENP5MOVBxD+l6sEG4zoLp" crossorigin="anonymous"></script>

</body>

</html>