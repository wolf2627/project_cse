<!-- <?php

// $pdfpath = "/required/files/industrialvisit.pdf";
// $pdffile = $_SERVER['DOCUMENT_ROOT'] . $pdfpath;

// if (file_exists($pdffile)) {
//     // use iframes to display PDF
//     echo "<iframe src='$pdfpath' width='100%' height='1000px'></iframe>";
// } else {
//     http_response_code(404);
//     echo "File not found.";
// }
?>
 -->

 <style>
    .report-item {
        background-color: #ccc;
        padding: 10px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .report-item:hover {
        transform: scale(1.03);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    .serial-number {
        width: 40px;
        text-align: center;
        font-weight: bold;
        background-color: #bfbfbf;
        padding: 10px;
        color: black;
    }

    .report-content {
        flex-grow: 1;
        padding-left: 10px;
    }

    a {
        text-decoration: none;
        color: black;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container">
    <h4 class="text-center mb-4 h4">Industrial Visit</h4>

    <div class="mt-4 d-flex">
        <div class="w-50">
            <div class="row">
                <?php
                $reports = [
                    ['year' => '2024-25', 'file' => '2023.pdf'],
                    ['year' => '2023-24', 'file' => '2022.pdf'],
                    ['year' => '2022-23', 'file' => '2020.pdf'],
                ];
                foreach ($reports as $index => $report): ?>
                    <div class="col-12 report-item" onclick="window.open('/required/files/<?php echo $report['file']; ?>', '_blank')">
                        <div class="serial-number"><?php echo $index + 1; ?>.</div>
                        <div class="report-content">
                            <a href="/required/files/<?php echo $report['file']; ?>" target="_blank"><?php echo $report['year']; ?> Report</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>