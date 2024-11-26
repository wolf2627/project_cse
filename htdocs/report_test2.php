<?php

include 'libs/load.php';
require 'vendor/autoload.php';

use TCPDF\TCPDF;
use PhpOffice\PhpWord\PhpWord;

// MongoDB connection
$database = Database::getConnection();

$testid = '673b3494bf7730ef8c0612b3';

$classReport = new ClassReport();

$testDetails = Test::getTestDetails($testid);

$sectionWiseReport = $classReport->getSectionWiseReport($testid); // Fixed typo from $test_id to $testid

$reportFormat = $_GET['format'] ?? 'pdf'; // Default to PDF if no format is passed

if ($reportFormat === 'pdf') {
    $pdf = new \TCPDF(); // Using TCPDF class
    $pdf->AddPage();

    // Set page margins
    $pdf->SetMargins(5, 10, 5);  // Reduced margins to fit content
    $pdf->SetAutoPageBreak(TRUE, 10); // Smaller bottom margin for auto page breaks

    // Page Header
    $pdf->SetFont('helvetica', 'B', 16); // Larger font for title
    $pdf->Cell(0, 10, "PSNA COLLEGE OF ENGINEERING AND TECHNOLOGY", 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 12); // Larger font for title
    $pdf->Cell(0, 10, "(An Autonomous Institution Affiliated to Anna University, Chennai)", 0, 1, 'C');

    $pdf->SetFont('helvetica', 'B', 14); // Larger font for title
    $pdf->Cell(0, 10, "$testDetails->testname - Report", 0, 1, 'C');

    $pdf->SetFont('helvetica', 'B', 12); // Larger font for title

    // Set the width for each piece of information
    $cellWidth = 70; // Adjust the width to your preference

    // Print all the values on the same line
    $pdf->Cell($cellWidth, 10, "Batch: $testDetails->batch", 0, 0, 'C'); // No line break
    $pdf->Cell($cellWidth, 10, "Semester: $testDetails->semester", 0, 0, 'C'); // No line break
    $pdf->Cell($cellWidth, 10, "Department: $testDetails->department", 0, 1, 'C'); // Line break after this cell



    if (!empty($sectionWiseReport)) {
        foreach ($sectionWiseReport as $section => $data) {
            $pdf->setFont('helvetica', 'B', 11);
            $pdf->SetX(15);
            $pdf->Cell(0, 10, "Section: $section", 0, 1, 'L'); // Centered Section Name
            $pdf->Ln(2);

            // Table header
            $pdf->SetFont('helvetica', 'B', 8); // Smaller font for header
            $pdf->SetX(15); // Set the starting X position for centering
            $pdf->Cell(20, 10, "Subject Code", 1, 0, 'C');
            $pdf->Cell(60, 10, "Subject Name", 1, 0, 'C');
            $pdf->Cell(40, 10, "Faculty", 1, 0, 'C');
            $pdf->Cell(15, 10, "Appeared", 1, 0, 'C');
            $pdf->Cell(15, 10, "Pass", 1, 0, 'C');
            $pdf->Cell(15, 10, "Fail", 1, 0, 'C');
            $pdf->Cell(15, 10, "Pass %", 1, 0, 'C');
            $pdf->Ln();

            // Loop through each subject data and print the rows
            foreach ($data['Subjects'] as $subjectData) {
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetX(15); // Set the starting X position for centering
                $pdf->Cell(20, 10, $subjectData['Subject Code'], 1, 0, 'C');
                $pdf->Cell(60, 10, $subjectData['Subject Name'], 1, 0, 'L');
                $pdf->Cell(40, 10, $subjectData['Faculty Name'], 1, 0, 'L');
                $pdf->Cell(15, 10, $subjectData['Appeared Students'], 1, 0, 'C');
                $pdf->Cell(15, 10, $subjectData['Pass Count'], 1, 0, 'C');
                $pdf->Cell(15, 10, $subjectData['Fail Count'], 1, 0, 'C');
                $pdf->Cell(15, 10, $subjectData['Pass Percentage'], 1, 0, 'C');
                $pdf->Ln();
            }

            $pdf->Ln(5); // Add some space between sections
        }

        $pdf->Ln(25); // Add some space between sections

        // Set the width for each piece of information
        $cellWidth = 100; // Adjust the width to your preference

        // Print all the values on the same line
        $pdf->setFont('helvetica', 'B', 9);
        $pdf->Cell($cellWidth, 10, "Professor Incharge", 0, 0, 'C'); // No line break
        $pdf->Cell($cellWidth, 10, "Head of the Department", 0, 0, 'C'); // No line break
        $pdf->SetY(-10);

        // Set font for the footer (optional)
        $pdf->SetFont('helvetica', 'I', 8);

        // Print the current date and time at the footer
        $dateTime = date('Y-m-d H:i:s');  // Format: YYYY-MM-DD HH:MM:SS
        $pdf->Cell(0, 10, 'Generated on: ' . $dateTime, 0, 0, 'C'); // Centered text

    } else {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, "No data available", 0, 1, 'C');
    }
    ob_end_clean(); // Clean any previous output
    $pdf->Output("performance_report.pdf", "I"); // Stream PDF for preview
}
