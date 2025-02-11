<?php

include 'libs/load.php';

// $title = "Test Contest";
// $description = "This is a test contest";
// $contestType = "test";
// $rounds = [
//     [
//         "round_number" => 1,
//         "title" => "Round 1",
//         "description" => "This is round 1",
//         "start_time" => "2021-06-01 00:00:00",
//         "end_time" => "2021-06-15 23:59:59",
//     ],
//     [
//         "round_number" => 2,
//         "title" => "Round 2",
//         "description" => "This is round 2",
//         "start_time" => "2021-06-16 00:00:00",
//         "end_time" => "2021-06-30 23:59:59",
//     ]
// ];
// $startTime = "2021-06-01 00:00:00";
// $endTime = "2021-06-30 23:59:59";
// $registrationDeadline = "2021-06-15 23:59:59";
// $facultyId = "1012";

// $result = Contest::createContest($title, $description, $contestType, $rounds ,$startTime, $endTime, $registrationDeadline, $facultyId);
// $result = Contest::changeStatus('67ab889266a1577e120eb4a3', 'upcoming');

try {
    // $result = ContestRegistration::registerForContest('67ab889266a1577e120eb4a3', '92132213026');

    // $result = ContestRegistration::confirmRegistration('67ab889266a1577e120eb4a3', '92132213026', '1012');

    // $contest = new Contest("67ab404af156da0ef30034b2");
    // $result = $contest->setJuries(['1012', '1013']);

    // $result = $contest->getJuries();

    $result = ContestQuestions::addCodingQuestion('coding', 'Test Coding Question', 'This is a test coding question', 'Input Format', 'Output Format', [['input' => '1', 'output' => '2']], 'easy', '67ab889266a1577e120eb4a3', '67ab889266a1577e120eb4a4');

    print_r($result);
} catch (Exception $e) {
    echo $e->getMessage();
}

// Session::ensureLogin();

// Session::ensureRole('admin');

// Session::renderPage();



?>
