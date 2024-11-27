<?php

include 'libs/load.php';
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;

// MongoDB connection
$database = Database::getConnection();

if (isset($_POST['test_id'])) {
    $testid = $_POST['test_id'];
} else {
    throw new Exception("Test ID not provided");
}

$classReport = new ClassReport();

//Get test details from the test id
$testDetails = Test::getTestDetails($testid);

//TODO: Get data via post, instead of calling the method again which increases the load
$sectionWiseReport = $classReport->getSectionWiseReport($testid);

$reportFormat = $_GET['format'] ?? 'pdf'; // Default to PDF if no format is passed

// Custom TCPDF class to override the Footer method
class CustomPDF extends \TCPDF
{

    public function Header()
    {
        // Do nothing here to prevent the default header
    }

    // Override the Footer method to add the generated date and time
    public function Footer()
    {
        // Set font for the footer
        $this->SetFont('helvetica', 'I', 8);

        // Move to the bottom of the page
        $this->SetY(-15);

        // Set the timezone to IST (Indian Standard Time)
        date_default_timezone_set('Asia/Kolkata');
        $currentDateTime = date('Y-m-d H:i:s');  // Format: YYYY-MM-DD HH:MM:SS

        // Print the footer text with the generated date and time
        $this->Cell(0, 10, 'Generated on: ' . $currentDateTime, 0, 0, 'R');
    }
}

if ($reportFormat === 'pdf') {
    $pdf = new CustomPDF(); // Use the custom TCPDF class
    $pdf->AddPage();

    // Set page margins
    $pdf->SetMargins(5, 10, 5);  // Reduced margins to fit content
    $pdf->SetAutoPageBreak(TRUE, 10); // Smaller bottom margin for auto page breaks

    // Page Header
    $pdf->SetFont('helvetica', 'B', 16); // Larger font for title
    $pdf->Cell(0, 10, "PSNA COLLEGE OF ENGINEERING AND TECHNOLOGY", 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 12); // Larger font for title
    $pdf->Cell(0, 5, "(An Autonomous Institution Affiliated to Anna University, Chennai)", 0, 1, 'C');

    $pdf->Ln(5); // Add some space between sections

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
        $pdf->Cell($cellWidth, 10, "Year Incharge", 0, 0, 'C'); // No line break
        $pdf->Cell($cellWidth, 10, "Head of the Department", 0, 0, 'C'); // No line break

        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 16); // Larger font for title
        $pdf->Cell(0, 10, "PSNA COLLEGE OF ENGINEERING AND TECHNOLOGY", 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 12); // Larger font for title
        $pdf->Cell(0, 5, "(An Autonomous Institution Affiliated to Anna University, Chennai)", 0, 1, 'C');

        $pdf->Ln(5); // Add some space between sections

        $pdf->SetFont('helvetica', 'B', 14); // Larger font for title

        $data = $classReport->SeparateFailedStudentsBySubjects($sectionWiseReport);

        $pdf->SetFont('helvetica', 'B', 12); // Larger font for title

        // Set the width for each piece of information
        $cellWidth = 70; // Adjust the width to your preference

        // Print all the values on the same line
        $pdf->Cell($cellWidth, 10, "Batch: $testDetails->batch", 0, 0, 'C'); // No line break
        $pdf->Cell($cellWidth, 10, "Semester: $testDetails->semester", 0, 0, 'C'); // No line break
        $pdf->Cell($cellWidth, 10, "Department: $testDetails->department", 0, 1, 'C'); // Line break after this cell

        $pdf->SetFont('helvetica', 'B', 12); // Larger font for title

        $pdf->Cell(0, 10, "$testDetails->testname - Subject Wise Failed Students List", 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 8); // Smaller font for header
        $pdf->SetX(10); // Set the starting X position for centering

        // Loop through each subject and display its details
        foreach ($data as $subject_code => $subject_data) {
            // Display subject name as header
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetX(20);
            $pdf->Cell(0, 10, $subject_data["Subject Name"] . " - " . $subject_code, 0, 1, 'L');

            // Start the table for the subject
            $pdf->SetFont('helvetica', 'B', 8); // Smaller font for header
            $pdf->SetX(20); // Set the starting X position for centering
            $pdf->Cell(15, 10, "S.No", 1, 0, 'C');
            $pdf->Cell(30, 10, "Reg No", 1, 0, 'C');
            $pdf->Cell(80, 10, "Student Name", 1, 0, 'C');
            $pdf->Cell(20, 10, "Section", 1, 0, 'C');
            $pdf->Cell(20, 10, "Marks", 1, 0, 'C');
            $pdf->Ln();
            $count = 1;
            // Loop through each failed student for the current subject
            foreach ($subject_data["Failed Students"] as $failed_student) {
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetX(20); // Set the starting X position for centering
                $pdf->Cell(15, 7, $count, 1, 0, 'C');
                $pdf->Cell(30, 7, $failed_student["Reg No"], 1, 0, 'C');
                $pdf->Cell(80, 7, $failed_student["Student Name"], 1, 0, 'L');
                $pdf->Cell(20, 7, $failed_student["Section"], 1, 0, 'C');
                $pdf->Cell(20, 7, $failed_student["Marks"], 1, 0, 'C');
                $pdf->Ln();
                $count++;
            }
            $pdf->Ln(5); // Add some space between sections
        }

        $pdf->AddPage();

        $pdf->SetFont('helvetica', 'B', 16); // Larger font for title
        $pdf->Cell(0, 10, "PSNA COLLEGE OF ENGINEERING AND TECHNOLOGY", 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 12); // Larger font for title
        $pdf->Cell(0, 5, "(An Autonomous Institution Affiliated to Anna University, Chennai)", 0, 1, 'C');

        $pdf->Ln(5); // Add some space between sections


        $pdf->SetFont('helvetica', 'B', 12); // Larger font for title

        // Set the width for each piece of information
        $cellWidth = 70; // Adjust the width to your preference

        // Print all the values on the same line
        $pdf->Cell($cellWidth, 10, "Batch: $testDetails->batch", 0, 0, 'C'); // No line break
        $pdf->Cell($cellWidth, 10, "Semester: $testDetails->semester", 0, 0, 'C'); // No line break
        $pdf->Cell($cellWidth, 10, "Department: $testDetails->department", 0, 1, 'C'); // Line break after this cell

        $failureData = $classReport->calculatefailedsubjects($sectionWiseReport);

        $pdf->SetFont('helvetica', 'B', 14); // Larger font for title

        $pdf->SetFont('helvetica', 'B', 12); // Larger font for title
        $pdf->Cell(0, 10, "$testDetails->testname - Failed Students Based on Count", 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 8); // Smaller font for header
        $pdf->SetX(8); // Set the starting X position for centering

        $colheight = 8;

        foreach ($failureData as $failed_subject_count => $category_data) {
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetX(8); // Set the starting X position for centering
            $pdf->Cell(0, 10, "Category: Failed in $failed_subject_count Subject(s) - Total count $category_data[count]", 0, 1, 'L');
            //if($failed_student_count >= 5) {$colheight = $colheight+1;}

            // Start the table for this category
            $pdf->SetFont('helvetica', 'B', 8); // Smaller font for header
            $pdf->SetX(8); // Set the starting X position for centering
            $pdf->Cell(10, 6, "S.No.", 1, 0, 'C');
            $pdf->Cell(30, 6, "Reg No", 1, 0, 'C');
            $pdf->Cell(42, 6, "Student Name", 1, 0, 'C');
            $pdf->Cell(13, 6, "Section", 1, 0, 'C');
            $pdf->Cell(100, 6, "Subjects", 1, 0, 'C');
            $pdf->Ln();

            $count = 1;

           
            // Loop through each student in this category
            foreach ($category_data['students'] as $student) {
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetX(8); // Set the starting X position for centering
                $pdf->Cell(10, $colheight, $count, 1, 0, 'C');
                $pdf->Cell(30, $colheight, $student["Reg No"], 1, 0, 'C');
                $pdf->Cell(42, $colheight, $student["Student Name"], 1, 0, 'L');
                $pdf->Cell(13, $colheight, $student["Section"], 1, 0, 'C');
                
                $count++;

                // Collect the failed subjects into a comma-separated list
                $failed_subjects_list = array();
                foreach ($student["Failed Subjects"] as $subject) {
                    $failed_subjects_list[] = $subject["Subject Name"];
                }

                $pdf->MultiCell(100, $colheight, implode(", ", $failed_subjects_list), 1, 'L');
                //$pdf->Ln();
            }
            $pdf->Ln(5); // Add some space between categories

            $colheight += 0.8 ;
        }

    } else {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, "No data available", 0, 1, 'C');
    }
    ob_end_clean(); // Clean any previous output

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="classwise_report.pdf"');

    // Assuming $pdf contains the PDF content generated by TCPDF or similar library
    echo $pdf->Output('classwise_report.pdf', 'S'); // 'S' means output as a string (binary content)

    exit();
}
