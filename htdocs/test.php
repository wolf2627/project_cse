<pre>
<?php

include 'test.class.php';

use MongoDB\BSON\ObjectId;

// $test = new Test();

// $test_id = '673aa09c95208f634c0fdcd2';

// $testDetails = $test->getTestDetails($test_id);

// echo "Test details: " . json_encode($testDetails) . "\n";

$pipeline = [['$match' => ['test_id' => new ObjectId('673aa529a80f94e05d040642')]], ['$unwind' => ['path' => '$marks']], ['$lookup' => ['from' => 'tests', 'localField' => 'test_id', 'foreignField' => '_id', 'as' => 'test_details']], ['$addFields' => ['marks.pass' => ['$gte' => ['$marks.marks', ['$toInt' => '12']]], 'marks.category' => ['$switch' => ['branches' => [['case' => ['$gte' => ['$marks.marks', 90]], 'then' => 'Above 90'], ['case' => ['$gte' => ['$marks.marks', 75]], 'then' => 'Above 75'], ['case' => ['$gte' => ['$marks.marks', 60]], 'then' => 'Above 60']], 'default' => 'Below 60']], 'test_details.subject_code' => ['$arrayElemAt' => [['$arrayElemAt' => ['$test_details.subjects.subject_code', 0]], 0]]]], ['$group' => ['_id' => '$marks.category', 'student_count' => ['$sum' => 1], 'students' => ['$push' => ['reg_no' => '$marks.reg_no', 'name' => '$marks.studentname', 'marks' => '$marks.marks', 'pass' => '$marks.pass']], 'test_details' => ['$first' => ['testname' => ['$arrayElemAt' => ['$test_details.testname', 0]], 'subject_code' => ['$arrayElemAt' => ['$test_details.subject_code', 0]], 'totalmarks' => ['$arrayElemAt' => ['$test_details.totalmarks', 0]], 'passmarks' => ['$arrayElemAt' => ['$test_details.passmarks', 0]]]]]]];
$conn = Database::getConnection()->marks;

$cursor = $conn->aggregate($pipeline);

foreach ($cursor as $doc) {
    echo "Category: " . $doc['_id'] . "\n";

    echo "Number of Students: " . $doc['student_count'] . "\n";

    echo "Test Name: " . $doc['test_details']['testname'] . "\n";

    echo "Subject Code: " . $doc['test_details']['subject_code'] . "\n";

    echo "Students:\n";

    foreach ($doc['students'] as $student) {
        echo "  Name: " . $student['name'] . ", Marks: " . $student['marks'] . ", Pass: " . ($student['pass'] ? "Yes" : "No") . "\n";
    }

    echo "-------------------\n\n";
}

foreach ($cursor as $doc){
    echo json_encode($doc) . "\n";
}


?>
</pre>