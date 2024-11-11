<pre>

<?php

use MongoDB\Collection;

require 'libs/load.php';


// $email = 'test@ex.com';
// $username = explode("@", $email)[0];
$password = 'jerry';

$username = 'jerry';

echo "<br> Username: $username";


// $db = Database::getConnection();

// $collection = $db->Auth;

// try {
//     $search_data = [
//         '$or' => [  // Use $or here instead of "or"
//             ['username' => $username],
//             ['email' => $username]  // If you want to search for either username or email
//         ]
//     ];

//     $result = $collection->findOne($search_data);

//     if ($result) {
//         echo "User found: {$result->username}";
//         print_r($result);
//     } else {
//         echo "User not found";
//     }

// } catch (Exception $e) {
//     echo "<br> Error: {$e->getMessage()}";
// }


// $db = Database::getConnection();


// $result = $collection->findOne(['username' => $username]);

// $result = Database::getArray($result);

// print_r($result);

// if(User::createUserLogin($username, $email, $password)){
//     echo "User created";
// } else {
//     echo "User creation failed";
// }

// $result = UserSession::authenticate($username, $password, "samplefingerprint");

// if($result){
//     echo $result;
//     echo "<br> Logged in";
// } else {
//     echo "Login failed";
// }

// UserSession::authorize('13332b33ac447dc79cabfd03db247290');



// if(Session::isAuthenticated()){
//     echo "<br> Authenticated";
// } else {
//     echo "<br> Not Authenticated";
// }
// // // $user = new User($username);

// // Session::loadTemplate('_signup');
// echo "<br> Success ";


?>

</pre>