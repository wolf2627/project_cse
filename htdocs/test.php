<pre>
<?php

include 'libs/load.php';

$Student = new Student('92132213026');


print_r($Student->getStudentDetails());

print_r($Student->getEnrolledClasses());

print_r(Essentials::loadSubjects());
?>
</pre>