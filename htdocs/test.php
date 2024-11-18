<pre>
<?php

require_once 'libs/load.php';

$faculty = new Faculty();

print_r($faculty->getClasses());

// print_r($faculty->getSubjects());

// print_r($faculty->getFacultyAssignedTests());

// print_r($_POST);

$result = $faculty->getMarks('2022-2026', '5', 'GE2C25', 'Serial Test 1', 'A');

echo "<br>";

print_r($result);

?>
</pre>