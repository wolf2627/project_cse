<pre>

<?php

include 'libs/load.php';

$role = new Role();

$roleName = 'student';

$roleCategory = 'student';

$roles = $role->getRoles();

print_r($roles);


// $classreport = new ClassReport();

// // Example usage
// $test_id = '673b3494bf7730ef8c0612b3'; // Test ID to be passed
// $report = $classreport->getSectionWiseReport($test_id);

// // print_r($report);


// $overall = $classreport->calculateOverallReport($report);

// // echo '<h1>Overall Report</h1>';

// foreach ($overall as $subject_code){
//     echo 'subject code : '.$subject_code['Subject Code'].'<br>';
// }


// print_r($overall);

?>

</pre>