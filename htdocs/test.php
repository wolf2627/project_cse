<pre>

<?php

include 'libs/load.php';


$classreport = new ClassReport();

// Example usage
$test_id = '673b3494bf7730ef8c0612b3'; // Test ID to be passed
$report = $classreport->getSectionWiseReport($test_id);

// //print_r($report);
$failedStudents = $classreport->SeparateFailedStudentsBySubjects($report);

// Output the combined result of failed students by subject
//print_r($failedStudents);

$data = $failedStudents;

// $result = $classreport->calculatefailedsubjects($report);

// print_r($result);

// Display failed students for all subjects

// Function to display failed students subject-wise with subject as header
// function displayFailedStudentsSubjectWise($failed_students_by_subject) {
//     // Loop through each subject and display its details
//     foreach ($failed_students_by_subject as $subject_code => $subject_data) {
//         // Display subject name as header
//         echo "<h3>" . $subject_data["Subject Name"] . ":</h3>";

//         // Start the table for the subject
//         echo "<table border='1' style='margin-bottom: 20px;'>";
//         echo "<tr><th>Reg No</th><th>Student Name</th><th>Section</th><th>Marks</th></tr>";

//         // Loop through each failed student for the current subject
//         foreach ($subject_data["Failed Students"] as $failed_student) {
//             echo "<tr>";
//             echo "<td>" . $failed_student["Reg No"] . "</td>";
//             echo "<td>" . $failed_student["Student Name"] . "</td>";
//             echo "<td>" . $failed_student["Section"] . "</td>";
//             echo "<td>" . $failed_student["Marks"] . "</td>";
//             echo "</tr>";
//         }

//         // Close the table for the current subject
//         echo "</table>";
//     }
// }

// // Call the function to display the subject-wise table
// displayFailedStudentsSubjectWise($failedStudents);

$result = $classreport->calculatefailedsubjects($report);

print_r($result);

// function displayFailureCategories($failure_categories) {
//     // Check if there are any failure categories to display
//     if (empty($failure_categories)) {
//         echo "No students failed any subjects.";
//         return;
//     }

//     // Loop through failure categories
//     foreach ($failure_categories as $failed_subject_count => $category_data) {
//         // Display the category header (e.g., "Failed in 1 Subject")
//         echo "<h3>Category: Failed in $failed_subject_count Subject(s)</h3>";
        
//         // Start the HTML table for this category
//         echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
//         echo "<thead>
//                 <tr>
//                     <th>Reg No</th>
//                     <th>Student Name</th>
//                     <th>Section</th>
//                     <th>Subjects</th>
//                 </tr>
//               </thead>";
        
//         echo "<tbody>";
        
//         // Loop through each student in this category
//         foreach ($category_data['students'] as $student) {
//             echo "<tr>";
//             echo "<td>" . $student["Reg No"] . "</td>"; // Student Reg No
//             echo "<td>" . $student["Student Name"] . "</td>"; // Student Name
//             echo "<td>" . $student["Section"] . "</td>"; // Section
            
//             // Collect the failed subjects into a comma-separated list
//             $failed_subjects_list = array();
//             foreach ($student["Failed Subjects"] as $subject) {
//                 $failed_subjects_list[] = $subject["Subject Name"];
//             }

//             echo "<td>" . implode(", ", $failed_subjects_list) . "</td>"; // Subjects
//             echo "</tr>";
//         }
        
//         echo "</tbody>";
//         echo "</table>";
//         echo "<br>"; // Add a line break between categories
//     }
// }

// Call the function to display the failure categories
// displayFailureCategories($result);

?>
