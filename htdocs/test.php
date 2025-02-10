<pre>
<?php

include 'libs/load.php';

$faculty_id = Session::getUser()->getFacultyId();

$tutor = new Tutor($faculty_id);

$assignedClass = $tutor->getAssingedClass();

print_r($assignedClass);

$assignedStudents = $tutor->getTutorshipStudents();

print_r($assignedStudents);

?>
</pre>