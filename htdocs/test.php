<pre>
<?php

include 'libs/load.php';

$semester = '5';
$section = 'A';
$batch = '2022-2026';
$dept = 'CSE';

$result = essentials::loadStudents($semester, $section, $batch, $dept);

print_r($result);

?>
</pre>