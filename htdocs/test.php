<?php
// include 'libs/load.php';

// function createUserProfile($profileData, $username, $password) {
//     // Connect to MongoDB
//     $conn = Database::getConnection();
//     $userCollection = $conn->testuser;
//     $authCollection = $conn->testauth;

//     try {
//         // Insert profile data into the User collection
//         $userResult = $userCollection->insertOne($profileData);
//         $userId = $userResult->getInsertedId();

//         // Hash the password securely
//         $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

//         // Prepare auth data
//         $authData = [
//             'user_id' => $userId,
//             'username' => $username,
//             'password' => $hashedPassword,
//         ];

//         // Insert auth data into the Auth collection
//         $authCollection->insertOne($authData);

//         echo "User profile and authentication details created successfully.";

//     } catch (Exception $e) {
//         echo "Failed to create user profile: ", $e->getMessage();
//     }
// }


// // Example Usage:
// $profileData = [
//     'name' => 'John Doe',
//     'email' => 'john@example.com',
//     // other profile fields
// ];
// $username = 'john_doe';
// $password = 'securePassword123';

// createUserProfile($profileData, $username, $password);
?>

<?php
// include 'libs/load.php';

// function fetchUserProfile($username) {
//     // Connect to MongoDB
//     $conn = Database::getConnection();
//     $userCollection = $conn->testuser;
//     $authCollection = $conn->testauth;

//     try {
//         // Find the auth data using the username
//         $authData = $authCollection->findOne(['username' => $username]);

//         if ($authData === null) {
//             throw new Exception("User not found.");
//         }

//         // Get the user_id from the auth data
//         $userId = $authData['user_id'];

//         // Fetch the user profile using the user_id
//         $userProfile = $userCollection->findOne(['_id' => $userId]);

//         if ($userProfile === null) {
//             throw new Exception("User profile not found.");
//         }

//         // Combine user profile and authentication details
//         $userData = [
//             'profile' => $userProfile,
//             'auth' => $authData
//         ];

//         return $userData;  // Return both profile and auth data

//     } catch (Exception $e) {
//         echo "Failed to fetch user profile: ", $e->getMessage();
//     }
// }

// // Example Usage:
// $username = 'john_doe';
// $userData = fetchUserProfile($username);

// if ($userData) {
//     // Access user profile data and authentication data
//     $profile = $userData['profile'];
//     $auth = $userData['auth'];

//     echo "User Name: " . $profile['name'] . "\n";
//     echo "User Email: " . $profile['email'] . "\n";
//     echo "Username: " . $auth['username'] . "\n";
// }
?>

<?

echo "Loading...";

if (1731600863) {
    $login_time = DateTime::createFromFormat('Y-m-d H:i:s', 1731600863);
    if (300 > time() - $login_time->getTimestamp()) {
        echo "true";
        //return true;
    } else {
        echo "false";
        //return false;
    }
} else {
    throw new Exception("UserSession::isValid -> login time is null");
}

?>
