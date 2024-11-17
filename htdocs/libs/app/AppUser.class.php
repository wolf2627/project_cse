<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class AppUser
{
    private $collection = null;
    private $headers = [];
    private $conn = null;
    private $file = null;
    private $role = null;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function createUser($userType = "multiple", $role, $file)
    {
        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($file);
            $this->file = $file;

            // Get the active sheet
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Optionally, handle the header row (if your Excel file has headers)
            $file_header = array_shift($data);  // Remove the first row and treat it as the header

            // Set headers based on role
            if ($role == 'student') {
                $this->role = 'student';
                $this->collection = $this->conn->students;
                $this->headers = ['name', 'reg_no', 'email', 'roll_no', 'batch_start', 'batch_end', 'semester', 'section', 'department'];
            } else if ($role == 'faculty') {
                $this->role = 'faculty';
                $this->collection = $this->conn->faculties;
                $this->headers = ['name', 'email', 'department', 'designation', 'faculty_id'];
            } else {
                throw new Exception("Role not found");
            }

            if (empty($this->headers)) {
                throw new Exception("Headers not set");
            }

            $successCount = 0;
            $failureCount = 0;

            foreach ($data as $row) {
                // Combine row with headers
                $document = array_combine($this->headers, $row);

                // Try creating a user profile
                $result = $this->createUserProfile($document);

                if ($result) {
                    $successCount++;
                } else {
                    $failureCount++;
                }
            }

            // Optionally, return the success/failure count or other relevant info
            return [
                'success' => $successCount,
                'failure' => $failureCount
            ];

        } catch (Exception $e) {
            // Handle errors and throw them up the chain
            throw new Exception("Error creating users: " . $e->getMessage());
        }
    }

    private function createUserProfile($document)
    {
        try {
            // Add creation timestamp
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            $document['created_at'] = $now;
            $document['role'] = $this->role;

            // Insert the document into MongoDB
            $result = $this->collection->insertOne($document);
            $inserted_id = $result->getInsertedId();

            // Create the user login
            if (!AppUser::createUserLogin($document['name'], $document['email'], $inserted_id, $document['role'])) {
                throw new Exception("Error creating user login");
            }

            return true;
        } catch (Exception $e) {
            // Log and re-throw the error for the calling function to handle
            throw new Exception("Error creating user profile: " . $e->getMessage());
        }
    }

    public static function createUserLogin($username, $email, $user_id, $role)
    {
        try {
            $conn = Database::getConnection();
            $collection = $conn->Auth;

            // Prepare password and username
            $username = explode("@", $email)[0];
            $password = password_hash($username, PASSWORD_BCRYPT, ['cost' => 9]);

            $data = [
                'username' => $username,
                'user_id' => $user_id,
                'password' => $password,
                'role' => $role
            ];

            // Insert login details into the database
            $result = $collection->insertOne($data);
            return true;

        } catch (Exception $e) {
            // Handle any errors that may occur when creating user login
            throw new Exception("Error creating user login: " . $e->getMessage());
        }
    }
}
