<pre>

<?php

include 'libs/load.php';


$student = new Student();
$faculty = new Faculty();

$regno = $_GET['reg']; 

try {
    $result = $faculty->getFacultyDetails($regno);
    print_r($result);
} catch (Exception $e) {
    echo $e->getMessage();
}

?>


</pre>