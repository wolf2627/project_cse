<pre>

<?php

use MongoDB\Collection;

require 'libs/load.php';

// $email = 'test@ex.com';
// $username = explode("@", $email)[0];
// $password = 'password';


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
//     echo "Logged in";
// } else {
//     echo "Login failed";
// }


// // $user = new User($username);

// Session::loadTemplate('_signup');

?>

</pre>