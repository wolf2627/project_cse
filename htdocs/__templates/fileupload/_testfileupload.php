<?php

$base_dir = $_SERVER['DOCUMENT_ROOT'] . "/required/uploads/nba/";
$allowedTypes = array("jpg", "png", "jpeg", "pdf");

// Ensure the base directory exists
if (!is_dir($base_dir)) {
    mkdir($base_dir, 0777, true);
}

// Get existing categories excluding uncategorized files
function getCategories($directory) {
    return array_filter(glob($directory . '*', GLOB_ONLYDIR), 'is_dir');
}

// List uncategorized files
function listUncategorizedFiles($directory) {
    $allFiles = array_diff(scandir($directory), ['.', '..', 'upload_log.csv']);
    $files = array_filter($allFiles, function($file) use ($directory) {
        return is_file($directory . $file);
    });

    if (!empty($files)) {
        echo "<h3>Files</h3>";
        echo "<table class='table table-bordered'>
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Upload Time (Unknown)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>";

        foreach ($files as $file) {
            $filePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $directory . $file);
            echo "<tr>
                    <td><a href='$filePath' target='_blank'>$file</a></td>
                    <td> - </td>
                    <td><a href='?delete=$file&subdirectory=uncategorized' class='btn btn-danger btn-sm'>Delete</a></td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No uncategorized files found.</p>";
    }
}

// File Upload Handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    $subdirectory = trim($_POST['subdirectory']);
    if (empty($subdirectory)) {
        echo "<div class='alert alert-danger'>Please select or create a category.</div>";
        exit;
    }

    $subdirectory = preg_replace('/[^a-zA-Z0-9_-]/', '', $subdirectory);
    $target_dir = $base_dir . ($subdirectory === 'uncategorized' ? '' : $subdirectory . "/");

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    foreach ($_FILES['fileToUpload']['name'] as $key => $fileName) {
        $target_file = $target_dir . basename($fileName);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        date_default_timezone_set("Asia/Kolkata");
        $uploadTime = date("d-m-Y H:i:s");

        // File validations
        if (file_exists($target_file)) {
            echo "<div class='alert alert-warning'>File $fileName already exists in $subdirectory.</div>";
            continue;
        }

        if (!in_array($fileType, $allowedTypes)) {
            echo "<div class='alert alert-danger'>Invalid format: $fileName (Only JPG, JPEG, PNG, PDF allowed).</div>";
            continue;
        }

        // Upload file
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$key], $target_file)) {
            echo "<div class='alert alert-success'>File uploaded: 
                    <a href='" . str_replace($_SERVER['DOCUMENT_ROOT'], '', $target_file) . "' target='_blank'>$fileName</a>
                  </div>";

            // Save upload details in a log if not uncategorized
            if ($subdirectory !== 'uncategorized') {
                file_put_contents($target_dir . "upload_log.csv", "$fileName,$uploadTime\n", FILE_APPEND);
            }
        } else {
            echo "<div class='alert alert-danger'>Error uploading $fileName.</div>";
        }
    }
}

// File Deletion Handling
if (isset($_GET['delete']) && isset($_GET['subdirectory'])) {
    $fileToDelete = basename($_GET['delete']);
    $subdirectory = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['subdirectory']);
    $filePath = ($subdirectory === 'uncategorized') ? $base_dir . $fileToDelete : $base_dir . $subdirectory . "/" . $fileToDelete;

    if (file_exists($filePath)) {
        unlink($filePath);
        echo "<div class='alert alert-success'>File $fileToDelete deleted successfully from $subdirectory.</div>";

        if ($subdirectory !== 'uncategorized') {
            // Update log file
            $logFile = $base_dir . $subdirectory . "/upload_log.csv";
            $logData = array_map('str_getcsv', file($logFile));
            $updatedData = array_filter($logData, function($row) use ($fileToDelete) {
                return $row[0] !== $fileToDelete;
            });

            $fileHandle = fopen($logFile, 'w');
            foreach ($updatedData as $row) {
                fputcsv($fileHandle, $row);
            }
            fclose($fileHandle);
        }
    } else {
        echo "<div class='alert alert-danger'>File not found in $subdirectory.</div>";
    }
}

// Display Uploaded Files
function listUploadedFiles($directory) {
    $subdirs = getCategories($directory);

    if (empty($subdirs)) {
        echo "<p>No categories available.</p>";
        return;
    }

    foreach ($subdirs as $subdir) {
        $logFile = $subdir . "/upload_log.csv";
        $category = basename($subdir);

        echo "<h3>Category: $category</h3>";

        if (file_exists($logFile)) {
            $files = array_map('str_getcsv', file($logFile));
            echo "<table class='table table-bordered'>
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Upload Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($files as $file) {
                $filePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $subdir . "/" . $file[0]);
                echo "<tr>
                        <td><a href='$filePath' target='_blank'>{$file[0]}</a></td>
                        <td>{$file[1]}</td>
                        <td><a href='?delete={$file[0]}&subdirectory=$category' class='btn btn-danger btn-sm'>Delete</a></td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No files uploaded in this category yet.</p>";
        }
    }
}
?>

<!-- Upload Form -->
<div class="container mt-5">
    <h2>Upload Files</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label>Select files to upload (Single or Multiple):</label>
        <input class="form-control mb-3" type="file" name="fileToUpload[]" id="fileToUpload" multiple required>

        <!-- Dropdown to Select or Create Category -->
        <label>Select or Create Category:</label>
        <select class="form-select mb-3" id="existingCategory" name="subdirectory">
            <option value="">-- Select an existing category --</option>
            <option value="uncategorized">Uncategorized</option>
            <?php foreach (getCategories($base_dir) as $dir): ?>
                <option value="<?= basename($dir) ?>"><?= basename($dir) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Or Create a New Category:</label>
        <input class="form-control mb-3" type="text" id="newCategory" placeholder="Enter New Category Name">
        
        <input type="submit" value="Upload File(s)" name="submit" class="btn btn-primary">
    </form>
</div>

<!-- Display Uploaded Files -->
<div class="container mt-5">
    <?php 
    listUploadedFiles($base_dir); 
    listUncategorizedFiles($base_dir);
    ?>
</div>
