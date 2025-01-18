<pre>
<?php

include 'libs/load.php';

$tut = new Tutor('1012');

try {
    // $tut = Tutor::AssignTutor('1011', 'CSE', '2024-2028', 'A');
    // $tut = $tut->getTutorshipStudents();

    $tut = Tutor::getTutors();

    print_r($tut);

} catch (Exception $e) {
    echo $e->getMessage();
}

?>
</pre>