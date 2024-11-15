<?php
include 'libs/load.php';

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
<pre>
<?

// require 'vendor/autoload.php';
// include 'libs/load.php';

// use PhpOffice\PhpSpreadsheet\IOFactory;
// use MongoDB\Client;

// // MongoDB connection
// $db = Database::getConnection();
// $collection = $db->exceltest;

// // Path to the XLSX file
// $inputFileName = 'Test_data_sheet.xlsx';

// // Load the Excel file
// $spreadsheet = IOFactory::load($inputFileName);
// echo "Loaded Excel file successfully\n";

// // Get the active sheet
// $sheet = $spreadsheet->getActiveSheet();
// echo "Loaded active sheet successfully: " . $sheet->getTitle() . "\n";
// // Convert the data from the sheet into an array
// $data = $sheet->toArray();
// print_r($data);
// // Optionally, handle the header row (if your Excel file has headers)
// $headers = array_shift($data);  // Remove the first row and treat it as the header
// echo "headers: <br>";

// $headers[3] = "newheader";

// print_r($headers);

// echo "$headers[3]"."<br>";
// // Loop through each row and insert it into MongoDB
// foreach ($data as $row) {
//     // Combine row values with the headers
//     $document = array_combine($headers, $row);
//     print_r($document);
//     // Insert the document into MongoDB
//    // $collection->insertOne($document);
// }

// // echo "Data successfully imported to MongoDB!";
// echo "Operation Completed!";

// $conn = Database::getConnection();
// $collection = $conn->Auth;
// $result = AppUser::createUserLogin("jerry", "jerry@admin", null);

// if ($result) {
//     echo "User Created Successfully";
// } else {
//     echo "Failed to create user";
// }



?>
</pre>