<?php

$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/required/uploads/nba/";
$allowedTypes = array("jpg", "png", "jpeg", "pdf");

// Ensure the directory exists
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// File Upload Handling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    foreach ($_FILES['fileToUpload']['name'] as $key => $fileName) {
        $target_file = $target_dir . basename($fileName);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $fileSize = $_FILES['fileToUpload']['size'][$key];
        date_default_timezone_set("Asia/Kolkata");
        $uploadTime = date("d-m-Y H:i:s");
        

        // File validations
        if (file_exists($target_file)) {
            echo "<div class='alert alert-warning'>File $fileName already exists.</div>";
            continue;
        }

        // if ($fileSize > 500000) {
        //     echo "<div class='alert alert-danger'>File $fileName is too large.</div>";
        //     continue;
        // }

        if (!in_array($fileType, $allowedTypes)) {
            echo "<div class='alert alert-danger'>Invalid format: $fileName (Only JPG, JPEG, PNG, PDF allowed).</div>";
            continue;
        }

        // Upload file
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$key], $target_file)) {
            echo "<div class='alert alert-success'>File uploaded: 
                    <a href='" . str_replace($_SERVER['DOCUMENT_ROOT'], '', $target_file) . "' target='_blank'>$fileName</a>
                  </div>";

            // Save upload details in a log
            file_put_contents($target_dir . "upload_log.csv", "$fileName,$uploadTime\n", FILE_APPEND);
        } else {
            echo "<div class='alert alert-danger'>Error uploading $fileName.</div>";
        }
    }
}

// File Deletion Handling
if (isset($_GET['delete'])) {
    $fileToDelete = basename($_GET['delete']);
    $filePath = $target_dir . $fileToDelete;

    if (file_exists($filePath)) {
        unlink($filePath);
        echo "<div class='alert alert-success'>File $fileToDelete deleted successfully.</div>";

        // Update log file
        $logData = array_map('str_getcsv', file($target_dir . "upload_log.csv"));
        $updatedData = array_filter($logData, function($row) use ($fileToDelete) {
            return $row[0] !== $fileToDelete;
        });
        $fileHandle = fopen($target_dir . "upload_log.csv", 'w');
        foreach ($updatedData as $row) {
            fputcsv($fileHandle, $row);
        }
        fclose($fileHandle);
    } else {
        echo "<div class='alert alert-danger'>File not found.</div>";
    }
}

// Display Uploaded Files
function listUploadedFiles($directory) {
    $logFile = $directory . "upload_log.csv";

    if (file_exists($logFile)) {
        $files = array_map('str_getcsv', file($logFile));
        echo "<h3>Uploaded Files</h3>
              <table class='table table-bordered'>
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Upload Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>";
        foreach ($files as $file) {
            $filePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $directory . $file[0]);
            echo "<tr>
                    <td><a href='$filePath' target='_blank'>{$file[0]}</a></td>
                    <td>{$file[1]}</td>
                    <td><a href='?delete={$file[0]}' class='btn btn-danger btn-sm'>Delete</a></td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No files uploaded yet.</p>";
    }
}
?>

<!-- Upload Form -->
<div class="container mt-5">
    <h2>Upload Files</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <label>Select files to upload (Single or Multiple):</label>
        <input class="form-control mb-3" type="file" name="fileToUpload[]" id="fileToUpload" multiple required>
        <input type="submit" value="Upload File(s)" name="submit" class="btn btn-primary">
    </form>
</div>

<!-- Display Uploaded Files -->
<div class="container mt-5">
    <?php listUploadedFiles($target_dir); ?>
</div>

<!-- Bootstrap for UI -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> -->

<!-- have password for this page alone -->
<!-- <script>
    var password = prompt("Enter password to view this page");
    if (password !== "jabar") {
        window.location.href = "home";
    } else {
        document.body.style.display = "block";
    }
</script> -->