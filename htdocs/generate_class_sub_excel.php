<?php
require_once 'libs/load.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from $_POST
    $batch = $_POST['batch'];
    $semester = $_POST['semester'];
    $subject_code = $_POST['subject_code'];
    $testname = $_POST['test_name'];
    $section = $_POST['section'];
    $department = $_POST['department'];
    $faculty_id = $_POST['faculty_id'];

    // Initialize Faculty and get marks
    // $faculty = new Faculty();
    $registered_marks = Marks::getMarks($batch, $semester, $subject_code, $testname, $section, $department, $faculty_id);

    if (!$registered_marks) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'No data found']);
        exit;
    }

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setCellValue('A1', 'PSNA COLLEGE OF ENGINEERING AND TECHNOLOGY');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->setCellValue('A2', '(An Autonomous Institution Affiliated to Anna University, Chennai)');

    // Center align the college name
    $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
    $sheet->mergeCells('A1:D1');
    $sheet->mergeCells('A2:D2');

    $sheet->mergeCells('A3:D3');

    // Test Name and Subject
    $sheet->setCellValue('A4', $testname . ' Marks - ' . $subject_code);
    $sheet->mergeCells('A4:D4');
    $sheet->getStyle('A4')->getFont()->setBold(true);
    $sheet->getStyle('A4')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A4:D4')->getFont()->setBold(true);

    $sheet->setCellValue('A5', 'Department: ' . $department . '  Batch: ' . $batch . '  Semester: ' . $semester . '  Section: ' . $section);
    $sheet->mergeCells('A5:D5');

    $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');

    $sheet->mergeCells('A6:D6');

    $sheet->setCellValue('A7', 'S.No.');
    $sheet->setCellValue('B7', 'Reg. No.');
    $sheet->setCellValue('C7', 'Student Name');
    $sheet->setCellValue('D7', 'Marks');

    $sheet->getStyle('A7:D7')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A7:D7')->getFont()->setBold(true);
    $sheet->getStyle('A7:D7')->getBorders()->getAllBorders()->setBorderStyle('thin');

    // Populate data rows
    $row = 8; // Start from the 7th row (after headers)
    foreach ($registered_marks->marks as $mark) {
        $sheet->setCellValue('A' . $row, $row - 7);
        $sheet->setCellValue('B' . $row, $mark['reg_no']);
        $sheet->setCellValue('C' . $row, $mark['studentname']);
        $sheet->setCellValue('D' . $row, $mark['marks']);;
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal('left');
        $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()->setBorderStyle('thin');
        $row++;
    }

    // Add footer

    $row++;

    $sheet->setCellValue('A' . $row, 'Total Students: ' . count($registered_marks->marks));
    $sheet->mergeCells('A' . $row . ':D' . $row);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('left');

    $Session_user = Session::getUser();

    $row++;

    $date = date('Y-m-d H:i:s');
    //conver to IST 
    $date = date('Y-m-d H:i:s', strtotime($date . ' + 5 hours 30 minutes'));

    $sheet->setCellValue('A' . $row, 'Generated on: ' . $date . " by " . $Session_user->getName() . "/" . $Session_user->getDesignation() . "," . $Session_user->getDepartment());
    $sheet->mergeCells('A' . $row . ':D' . $row);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal('left');
    $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setItalic(true);

    // Auto-size columns
    foreach (range('A', 'D') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Send the Excel file as a response
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="report_class_' . $section . '_subject_' . $subject_code . '.xlsx"');
    header('Cache-Control: max-age=0');

    // Write file to output (no need to use ob_flush or flush, just save to php://output)
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output'); // This sends the file directly to the browser

    exit; // Make sure no further output is sent
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method']);
}
