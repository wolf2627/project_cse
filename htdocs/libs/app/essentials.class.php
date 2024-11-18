<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class Essentials
{

    private $conn = null;
    private $collection = null;

    public function __construct()
    {
        // Assuming Database::getConnection() returns a valid MongoDB connection
        $this->conn = Database::getConnection();
    }


    public static function loadSemesters()
    {
        return [1, 2, 3, 4, 5, 6, 7, 8];
    }

    public static function loadBatches()
    {
        return ['2021-2025', '2022-2026', '2023-2027', '2024-2028'];
    }

    public static function loadSubjects()
    {
        // Assuming this function fetches subjects from the database
        $result = Database::getConnection()->subjects->find()->toArray();  // Convert the cursor to an array

        if (empty($result)) {
            return ["error" => "No subjects found"];
        }

        // Extract subject details for each subject in the collection
        $subjects = [];
        foreach ($result as $subject) {
            $subjects[] = [
                'subject_code' => $subject['subject_code'],
                'subject_name' => $subject['subject_name'],
                'type' => $subject['type']
            ];
        }

        return $subjects;
    }

    public static function loadSections()
    {
        return ['A', 'B', 'C', 'D'];
    }

    public static function loadDepartments()
    {
        return ['CSE'];
    }


    public static function loadStudents($semester, $section, $batch, $department)
    {

        // echo "Semester: $semester, Section: $section, Batch: $batch, Department: $dept .<br>.";
        try {
            // Get database connection
            $conn = Database::getConnection();

            // Access the students collection
            $collection = $conn->students;

            // Perform the query to find students matching the criteria
            // and sort by 'register_number' in ascending order (1 for ascending)
            $studentsCursor = $collection->find(
                [
                    'semester' => $semester,
                    'section' => $section,
                    'batch' => $batch,
                    'department' => $department
                ],
                [
                    'projection' => [
                        'name' => 1,               // Include the 'name' field
                        'reg_no' => 1,    // Include the 'register_number' field
                        '_id' => 0                 // Exclude the '_id' field (optional, but often desired)
                    ],
                    'sort' => ['reg_no' => 1]  // Sort by register_number in ascending order
                ]
            );

            // Convert the cursor to an array and return it
            $studentsCursorArray = iterator_to_array($studentsCursor);

            // Convert BSONDocument objects to plain arrays
            $students = [];
            foreach ($studentsCursorArray as $student) {
                $students[] = $student->getArrayCopy();  // Convert BSONDocument to array
            }


            // Return the array of students
            return $students;
        } catch (Exception $e) {
            // Handle error - log it or rethrow depending on your needs
            error_log($e->getMessage());
            return []; // Return an empty array in case of error
        }
    }


    public static function loadFaculties()
    {
        try {
            // Get database connection
            $conn = Database::getConnection();

            // Access the faculties collection
            $collection = $conn->faculties;

            // Perform the query to find faculties matching the criteria
            // and sort by 'faculty_id' in ascending order (1 for ascending)
            $facultiesCursor = $collection->find();

            // Convert the cursor to an array and return it
            $facultiesCursorArray = iterator_to_array($facultiesCursor);

            // Convert BSONDocument objects to plain arrays
            $faculties = [];
            foreach ($facultiesCursorArray as $faculty) {
                $faculties[] = $faculty->getArrayCopy();  // Convert BSONDocument to array
            }

            // Return the array of faculties
            return $faculties;
        } catch (Exception $e) {
            // Handle error - log it or rethrow depending on your needs
            error_log($e->getMessage());
            return []; // Return an empty array in case of error
        }
    }
}