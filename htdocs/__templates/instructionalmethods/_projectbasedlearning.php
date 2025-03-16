<h3 class="text-center">Project Based Learning</h3>

<div class="row mt-4 mb-4">
        <!-- YouTube Video Cards -->
        <div class="col-md-4">
            <div class="card">
                <div class="ratio ratio-16x9">
                    <!-- <iframe src="https://youtu.be/tKPs7lnZ22U?si=XKmMSuOfndNK6wYq" allowfullscreen></iframe> -->
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/Ce7GAuJBRUs?si=E8BW-0tRqEuyCtdq" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="card-body">
                    <h5 class="card-title">YouTube Video 1</h5>
                    <p class="card-text">Some details about this video.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="ratio ratio-16x9">
                    <!-- <iframe src="https://www.youtube.com/embed/YOUR_VIDEO_ID_2" allowfullscreen></iframe> -->
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/tKPs7lnZ22U?si=9F6dSX1xd3-zBXU0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="card-body">
                    <h5 class="card-title">YouTube Video 2</h5>
                    <p class="card-text">Some details about this video.</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="ratio ratio-16x9">
                    <!-- <iframe src="https://www.youtube.com/embed/YOUR_VIDEO_ID_3" allowfullscreen></iframe> -->
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/L7GhDHpve3U?si=zkxsyiVq7c3uH5cE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="card-body">
                    <h5 class="card-title">YouTube Video 3</h5>
                    <p class="card-text">Some details about this video.</p>
                </div>
            </div>
        </div>
    </div>


<?php

$pdfpath = "/required/files/projectbasedlearning.pdf";
$pdffile = $_SERVER['DOCUMENT_ROOT'] . $pdfpath;

if (file_exists($pdffile)) {
    // use iframes to display PDF
    echo "<iframe src='$pdfpath' width='100%' height='1000px'></iframe>";
} else {
    http_response_code(404);
    echo "File not found.";
}
?>

