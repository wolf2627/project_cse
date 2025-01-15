<?php

include 'libs/load.php';


// $tt = new Timetable();

// try {
//     $result = $tt->getStudentTimeTable('92132213026');
//     print_r($result);
// } catch (Exception $e) {
//     echo $e->getMessage();
// }

$att = new Attendance();

// try {
//     $att = new Attendance();

//     // Mark attendance
//     $result = $att->saveAttendance(
//         "CSE",
//         "1012",
//         "2025-01-10",
//         "Monday",
//         "GE2C25",
//         "A",
//         "09:45-10:35",
//         "2022-2026",
//         "5",
//         [
//             ['id' => '92132213026', 'status' => 'Present'],
//             ['id' => '92132213027', 'status' => 'Absent']
//         ]
//     );

//     print_r($result);
// } catch (Exception $e) {
//     echo $e->getMessage();
// }

// try {
//     // View attendance for a student
//     $attendance = $att->getStudentAttendance("92132213027");
//     echo "<br>Attendance for 92132213027: <br>";
//     print_r($attendance);
// } catch (Exception $e) {
//     echo $e->getMessage();
// }

// try {

//     $att = new Attendance();

//     // Notify faculty of pending attendances
//     $pending = $att->getPendingAttendance("1012");
//     echo "<br>Pending Attendance for 1012: <br>";
//     print_r($pending);
// } catch (Exception $e) {
//     echo $e->getMessage();
// }


// try {
//     // get marked sessions

//     $att = new Attendance();

//     $marked = $att->getMarkedSessions("1012");
//     echo "<br>Marked Sessions for 1012: <br>";
//     print_r($marked);

//     echo "<br>============================================================<br>";


//     foreach($marked as $session) {
//         $result = $att->getAttendance($session['_id']);

//         echo "<br>Attendance for session: {$session['_id']}<br>";

//         print_r($result);

//     }


// } catch (Exception $e) {
//     echo $e->getMessage();
// }

// try {
//     // Calculate overall attendance percentage
//     $percentage = $att->getOverallAttendancePercentage("92132213027");
//     echo "Overall Attendance Percentage: $percentage%";
// } catch (Exception $e) {
//     echo $e->getMessage();
// }

// $student= new Student('92132213026');

// $result = $student->getStudentDetails('92132213026');

// print_r($result);

// $student = new Student('92132213026');

// $enrolled_classes = $student->getEnrolledClasses('active');
// print_r($enrolled_classes);



// $class = new Classes();

// try {
//     $result = $class->getClasses('1012');
//     print_r($result);
// } catch (Exception $e) {
//     echo $e->getMessage();
// }


// $faculty = new Faculty();
// $facultyId = $faculty->getFacultyId(); // Assume this fetches the logged-in faculty's ID.

// $att = new Attendance();
// $pending = $att->getPendingAttendance($facultyId);

// $combinedData = [];

// foreach($pending as $class) {
//     $cls = new Classes();
//     $details = $cls->getClassDetails($class['class_id']);
//     $class['class_details'] = $details;
//     $combinedData[] = $class;
// }


// print_r($combinedData);


// $result = Classes::getClasses('1012');

// print_r($result);


$att = new Attendance();

// try {
//     $result = $att->getSessionDetails('6780c664ac2a783b160fe681');
//     print_r($result);
// } catch (Exception $e) {
//     echo $e->getMessage();
// }

// $tt = new TimeTable();

// try {
//     $result = $tt->getFacultyTimeTable('1012');
//     print_r($result);
// } catch (Exception $e) {
//     echo $e->getMessage();
// }


// try {
//     $att = new Attendance();

//     // // Mark attendance
//     // $result = $att->saveAttendance(
//     //     "CSE",
//     //     "1012",
//     //     "2025-01-10",
//     //     "Monday",
//     //     "GE2C25",
//     //     "A",
//     //     "09:45-10:35",
//     //     "2022-2026",
//     //     "5",
//     //     [
//     //         ['id' => '92132213026', 'status' => 'Present'],
//     //         ['id' => '92132213027', 'status' => 'Absent']
//     //     ]
//     // );

//     $result = $att->markSession('1012', '2025-01-14', 'Tues', '09:40-10:35','673ae822f8cb080248004c27');

//     print_r($result);
// } catch (Exception $e) {
//     echo $e->getMessage();
// }

// print_r(Faculty::getAllFaculties());

// $cr = new Crons();

// $cr->updateAttendance();

// $tt = new TimeTable();
// print_r($tt->getFacultyTimeTable('1012', 'Wednesday'));
// $f = new Faculty('1012');
// try {
//     $result = $f->getAssignedStudents('CY2124', '2022-2026', '5', 'A', 'CSE', '1012');

//     print_r($result);

// } catch (Exception $e) {
//     echo $e->getMessage();
// }

// $day = date('l');
// $date = date('Y-m-d');

// $logFile = get_config('absolute_path').'/logs/activity.log';
// $logEntry = "[test] Date: $date, Day: $day, Time: " . date('H:i:s') . "\n";
// file_put_contents($logFile, $logEntry, FILE_APPEND);

Log::dolog("Test log message", 'TEST', True);

?>
