<?php
$folder = 'required/uploads/e-content'; // Change this to your folder name
$files = scandir($folder);
$materials = array();

// Filter out only files
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..' && is_file("$folder/$file") && pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
        $materials[] = $file;
        // echo $file;
    }
}
?>


<div class="container mt-5">
    <h2 class="mb-4">Available PDF Materials</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Material Name</th>
                <th>Download Link</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materials as $index => $material): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($material) ?></td>
                    <td><a href="<?= $folder . '/' . $material ?>" class="btn btn-primary btn-sm" download>Download</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>