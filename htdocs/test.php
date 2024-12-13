<pre>

<?php

$result = ["message" => "Permissions removed"];

$finalResult = [];

$errorMessage = "Permissions removed";

if(preg_match('/\b(?:not|no)\b/i', $errorMessage)){
    echo "Match found";
}
// Adding the success key-value pair
$finalResult['success'] = true;

// Overwriting $finalResult with the $result data
$finalResult['result'] = $result;

print_r($finalResult);

?>

</pre>