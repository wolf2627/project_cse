<?php
include 'libs/load.php';
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<!-- load head -->

<? load_template('head'); ?>

<body>
  <!-- load mode toggle -->
  <? load_template('modetoggle'); ?>

  <!-- load svg for using icons -->
  <? load_template('svg'); ?>

  <!-- load header -->
  <? load_template('header'); ?>

  <div class="container-fluid">
    <div class="row">
      <!-- load sidebar -->
      <? load_template('sidebar'); ?>

      <!-- load navbar -->
      <? load_template('navbar'); ?>

      <? load_template('dashboard'); ?>
      <!-- load dashboard -->
    </div>
  </div>
  <script src="assets/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.js" integrity="sha384-eI7PSr3L1XLISH8JdDII5YN/njoSsxfbrkCTnJrzXt+ENP5MOVBxD+l6sEG4zoLp" crossorigin="anonymous"></script>
  <script src="dashboard.js"></script>
</body>

</html>