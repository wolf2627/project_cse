<?php

$pdfpath = "/required/uploads/catergorypdf/Flipped Learning.pdf";
$pdffile = $_SERVER['DOCUMENT_ROOT'] . $pdfpath;

if (file_exists($pdffile)) {
    // use iframes to display PDF
    echo "<iframe src='$pdfpath' width='100%' height='1000px'></iframe>";
} else {
    http_response_code(404);
    echo "File not found.";
}
?>