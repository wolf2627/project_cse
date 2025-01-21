<pre>
<?php

include 'libs/load.php';

$student = new Student('92132213245');

try {
  $tt = new TimeTable();
    $result = $tt->getStudentTimeTable('92132213245');
    print_r($result);

} catch (Exception $e) {
    echo $e->getMessage();
}

?>
</pre>