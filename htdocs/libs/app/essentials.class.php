<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class Essentials {

    private $conn = null;
    private $collection = null;

    public function __construct() {
        // Assuming Database::getConnection() returns a valid MongoDB connection
        $this->conn = Database::getConnection();
    }

    public function createSubjects($file) {
        try {
            // Load the spreadsheet
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Remove the header row from data
            $file_header = array_shift($data); // This is the header row, but not used

            // Define the headers for subjects
            $headers = ['subject_code', 'subject_name', 'type'];

            // Check if headers are set correctly
            if (empty($headers)) {
                throw new Exception("Headers not set");
            }

            // Initialize success and failure counters
            $successCount = 0;
            $failureCount = 0;

            // Process each row in the data
            foreach ($data as $row) {
                // Ensure the row has the same number of elements as the headers
                if (count($row) == count($headers)) {
                    // Combine row with headers into an associative array
                    $document = array_combine($headers, $row);

                    // Insert the subject into the database
                    $result = $this->insertSubject($document);

                    if ($result) {
                        $successCount++;
                    } else {
                        $failureCount++;
                    }
                } else {
                    // If row doesn't match headers, log it or handle as a failure
                    $failureCount++;
                    // Optionally, log the issue with this row
                    error_log("Row has an incorrect number of columns: " . implode(',', $row));
                }
            }

            // Return success and failure counts
            return [
                'success' => $successCount,
                'failure' => $failureCount
            ];

        } catch (Exception $e) {
            // Handle any exceptions that occur during the process
            return ['error' => $e->getMessage()];
        }
    }

    private function insertSubject($document) {
        try {
            // Insert the subject into the MongoDB collection
            $this->collection = $this->conn->subjects;
            $this->collection->insertOne($document);
            return true;
        } catch (Exception $e) {
            // Log or handle MongoDB insert failure
            error_log("Error inserting subject: " . $e->getMessage());
            return false;
        }
    }
}
