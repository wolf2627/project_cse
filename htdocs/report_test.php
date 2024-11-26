<?php
// Load required libraries
include 'libs/load.php';
require 'vendor/autoload.php';

use TCPDF\TCPDF;
use PhpOffice\PhpWord\PhpWord;

// MongoDB connection
$database = Database::getConnection();

// Parameters for filtering
$batch = $_GET['batch'] ?? '2024-2028';
$testName = $_GET['testName'] ?? 'Serial Test 1';
$reportFormat = $_GET['format'] ?? 'pdf'; // Default to PDF if not specified

// Aggregation pipeline for class performance
$classPipeline = [
    // Match the required batch and test_name
    ['$match' => ['batch' => $batch, 'test_name' => $testName]],

    // Unwind the marks array to process individual students
    ['$unwind' => '$marks'],

    // Lookup subject details
    ['$lookup' => [
        'from' => 'subjects',
        'localField' => 'subject_code', // Field in `marks`
        'foreignField' => 'subject_code', // Field in `subjects`
        'as' => 'subject_details'
    ]],

    // Lookup class details
    ['$lookup' => [
        'from' => 'classes',
        'localField' => 'class_id', // Field in `marks`
        'foreignField' => '_id', // Field in `classes`
        'as' => 'class_details'
    ]],

    // Unwind subject and class details to simplify calculations
    ['$unwind' => ['path' => '$subject_details', 'preserveNullAndEmptyArrays' => true]],
    ['$unwind' => ['path' => '$class_details', 'preserveNullAndEmptyArrays' => true]],

    // Group by class and subject
    ['$group' => [
        '_id' => ['class_id' => '$class_id', 'subject_id' => '$subject_code'],
        'class_name' => ['$first' => '$class_details.name'],
        'subject_name' => ['$first' => '$subject_details.subject_name'],
        'total_students' => ['$sum' => 1], // Count all students
        'appeared' => ['$sum' => ['$cond' => [['$ne' => ['$marks.marks', null]], 1, 0]]], // Non-null marks
        'pass_count' => ['$sum' => ['$cond' => [['$gte' => ['$marks.marks', 30]], 1, 0]]], // Marks >= 30
        'fail_count' => ['$sum' => ['$cond' => [['$lt' => ['$marks.marks', 30]], 1, 0]]], // Marks < 30
        'class_average' => ['$avg' => '$marks.marks'], // Average marks
        'below_average_count' => ['$sum' => ['$cond' => [['$lt' => ['$marks.marks', 30]], 1, 0]]],
        'average_count' => ['$sum' => ['$cond' => [['$and' => [['$gte' => ['$marks.marks', 30]], ['$lte' => ['$marks.marks', 45]]]], 1, 0]]],
        'above_average_count' => ['$sum' => ['$cond' => [['$gt' => ['$marks.marks', 45]], 1, 0]]]
    ]]
];

// Execute the pipeline
$classData = $database->marks->aggregate($classPipeline)->toArray();

// Handle missing data with NIL values
foreach ($classData as &$row) {
    $row['class_name'] = $row['class_name'] ?? 'NIL';
    $row['subject_name'] = $row['subject_name'] ?? 'NIL';
    $row['total_students'] = $row['total_students'] ?? 0;
    $row['appeared'] = $row['appeared'] ?? 0;
    $row['pass_count'] = $row['pass_count'] ?? 0;
    $row['fail_count'] = $row['fail_count'] ?? 0;
    $row['class_average'] = $row['class_average'] ?? 'NIL';
    $row['below_average_count'] = $row['below_average_count'] ?? 0;
    $row['average_count'] = $row['average_count'] ?? 0;
    $row['above_average_count'] = $row['above_average_count'] ?? 0;
}

// Generate reports
if ($reportFormat === 'pdf') {
    // PDF Report Generation
    $pdf = new \TCPDF();
    $pdf->AddPage();

    $pdf->SetFont('helvetica', '', 14);
    $pdf->Cell(0, 10, "PSNA COLLEGE OF ENGINEERING AND TECHNOLOGY", 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(0, 5, "(An Autonomous Institution Affiliated to Anna University, Chennai)", 0, 1, 'C');


    $pdf->Cell(0, 10, "SUBJECT DETAILS & FACULTY PERFORMANCE", 0, 1, 'C');

    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 12);
    // $pdf->Cell(0, 10, "Class: " . $row['class_name'], 0, 1);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(100, 10, "Subject Name", 1);
    $pdf->Cell(30, 10, "Appeared", 1);
    $pdf->Cell(30, 10, "Pass", 1);
    $pdf->Cell(30, 10, "Fail", 1);
    $pdf->Ln();

    foreach ($classData as $row) {
        $pdf->Cell(100, 10, $row['subject_name'], 1);
        $pdf->Cell(30, 10, $row['appeared'], 1);
        $pdf->Cell(30, 10, $row['pass_count'], 1);
        $pdf->Cell(30, 10, $row['fail_count'], 1);
        $pdf->Ln();
    }

    ob_end_clean();
    $pdf->Output("performance_report.pdf", "D");
    exit;
} elseif ($reportFormat === 'word') {
    // Word Report Generation
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    $section->addText("SUBJECT DETAILS & FACULTY PERFORMANCE", ['bold' => true, 'size' => 14]);

    foreach ($classData as $row) {
        $section->addText("Class: " . $row['class_name'], ['bold' => true, 'size' => 12]);
        $table = $section->addTable();

        $table->addRow();
        $table->addCell(5000)->addText("Subject Name");
        $table->addCell(2000)->addText("Appeared");
        $table->addCell(2000)->addText("Pass");
        $table->addCell(2000)->addText("Fail");

        $table->addRow();
        $table->addCell(5000)->addText($row['subject_name']);
        $table->addCell(2000)->addText($row['appeared']);
        $table->addCell(2000)->addText($row['pass_count']);
        $table->addCell(2000)->addText($row['fail_count']);
    }

    header("Content-Disposition: attachment; filename=performance_report.docx");
    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    $phpWord->save("php://output", "Word2007");
    exit;
} else {
    echo "Invalid format specified. Please use 'format=pdf' or 'format=word'.";
}