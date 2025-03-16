<header class="d-flex py-2">
    <ul class="nav nav-pills">
    <li class="nav-item"><a href="#" class="nav-link active">Experimental Learning</a></li>
        <li class="nav-item"><a href="/projectbasedlearning" class="nav-link">Project Based Learning</a></li>
        <li class="nav-item"><a href="internships" class="nav-link">Internships</a></li>
        <li class="nav-item"><a href="industrialvisit" class="nav-link">Industrial Visit</a></li>
    </ul>
</header>


<?php

$pdfpath = "/required/files/experimentallearning.pdf";
$pdffile = $_SERVER['DOCUMENT_ROOT'] . $pdfpath;

if (file_exists($pdffile)) {
    // Use iframe to display the PDF
    echo "<iframe src='$pdfpath' width='100%' height='1000px'></iframe>";
} else {
    http_response_code(404);
    echo "File not found.";
}

?>
