<?php
// Load required libraries
include 'libs/load.php';
require 'vendor/autoload.php';

use TCPDF\TCPDF;
use PhpOffice\PhpWord\PhpWord;

// MongoDB connection
$database = Database::getConnection();

$testid = '673b3494bf7730ef8c0612b3';

$classReport = new ClassReport();

$sectionWiseReport = $classReport->getSectionWiseReport($testid); // Fixed typo from $test_id to $testid

$reportFormat = $_GET['format'] ?? 'pdf'; // Default to PDF if no format is passed

// Generate reports
if ($reportFormat === 'pdf') {
    // PDF Report Generation
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
    $pdf->Cell(0, 10, "Subject Details & Faculty Performance", 0, 1, 'C');

    // Table header
    $pdf->SetFont('helvetica', 'B', 8); // Smaller font for header
    $pdf->Cell(50, 10, "Subject Code", 1, 0, 'C');
    $pdf->Cell(50, 10, "Subject Name", 1, 0, 'C');
    $pdf->Cell(25, 10, "Appeared", 1, 0, 'C');
    $pdf->Cell(25, 10, "Pass", 1, 0, 'C');
    $pdf->Cell(25, 10, "Fail", 1, 0, 'C');
    //$pdf->Cell(40, 10, "Failed Students", 1, 1, 'C'); // New column

    // Table Data
    $pdf->SetFont('helvetica', '', 8); // Smaller font for data rows

    // Loop through the sections and data
    foreach ($sectionWiseReport as $section => $data) {
        // Add section heading
        $pdf->Cell(0, 10, "Section: $section", 0, 1, 'L');
        $pdf->Ln(5);

        // Loop through the subjects
        foreach ($data['Subjects'] as $subjectData) {
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(50, 10, $subjectData['Subject Code'], 1, 0, 'C');
            $pdf->Cell(50, 10, $subjectData['Subject Name'], 1, 0, 'C');
            $pdf->Cell(25, 10, $subjectData['Appeared Students'], 1, 0, 'C');
            $pdf->Cell(25, 10, $subjectData['Pass Count'], 1, 0, 'C');
            $pdf->Cell(25, 10, $subjectData['Fail Count'], 1, 0, 'C');

        }
        $pdf->Ln(5); // Line break between sections
    }

    ob_end_clean(); // Clean any previous output
    $pdf->Output("performance_report.pdf", "I"); // Stream PDF for preview
    exit;
} elseif ($reportFormat === 'word') {
    // Word Report Generation
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    $section->addText("PSNA COLLEGE OF ENGINEERING AND TECHNOLOGY", ['bold' => true, 'size' => 16], ['align' => 'center']);
    $section->addText("(An Autonomous Institution Affiliated to Anna University, Chennai)", ['size' => 12], ['align' => 'center']);
    $section->addTextBreak(1);
    $section->addText("Subject Details & Faculty Performance", ['bold' => true, 'size' => 14]);

    // Table header
    $table = $section->addTable();
    $table->addRow();
    $table->addCell(2000)->addText("Subject Code");
    $table->addCell(4000)->addText("Subject Name");
    $table->addCell(1000)->addText("Appeared");
    $table->addCell(1000)->addText("Pass");
    $table->addCell(1000)->addText("Fail");
    $table->addCell(3000)->addText("Failed Students");

    // Loop through the sections and add rows to the Word table
    foreach ($sectionWiseReport as $section => $data) {
        // Add section title as a new row in Word
        $table->addRow();
        $table->addCell(8000, ['gridSpan' => 6])->addText("Section: $section", ['bold' => true, 'size' => 12]);

        // Loop through subjects
        foreach ($data['Subjects'] as $subjectData) {
            $table->addRow();
            $table->addCell(2000)->addText($subjectData['Subject Code']);
            $table->addCell(4000)->addText($subjectData['Subject Name']);
            $table->addCell(1000)->addText($subjectData['Appeared Students']);
            $table->addCell(1000)->addText($subjectData['Pass Count']);
            $table->addCell(1000)->addText($subjectData['Fail Count']);

            // Add Failed Students
            $failedStudents = !empty($subjectData['Failed Students']) ? implode(', ', $subjectData['Failed Students']) : 'No Failed Students';
            $table->addCell(3000)->addText($failedStudents);
        }
    }

    header("Content-Disposition: inline; filename=performance_report.docx"); // Stream inline in browser
    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    $phpWord->save("php://output", "Word2007"); // Output Word document directly
    exit;
} else {
    echo "Invalid format specified. Please use 'format=pdf' or 'format=word'.";
}
?>
