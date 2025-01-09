<pre>

<?php

include 'libs/load.php';


$tt = new Timetable();

try {
    $result = $tt->getStudentTimeTable('92132213026');
    print_r($result);
} catch (Exception $e) {
    echo $e->getMessage();
}





// $student = new Student('92132213026');

// $result = $student->getStudentDetails('92132213026');

// print_r($result);

// $student = new Student('92132213026');

// $enrolled_classes = $student->getEnrolledClasses();
// print_r($enrolled_classes);


?>
</pre>