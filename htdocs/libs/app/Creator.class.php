<?php


//TODO: Check and Remove these Lines
// require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class Creator {

    private $conn = null;

    public static function createSubjects($file)
    {
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
                    $document['created_at'] = (new MongoDB\BSON\UTCDateTime())->toDateTime()->format('Y-m-d H:i:s');
                    // Insert the subject into the database
                    $result = Creator::insertSubject($document);

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

    private static function insertSubject($document)
    {
        // Get the MongoDB connection
        $conn = Database::getConnection();
        try {
            // Insert the subject into the MongoDB collection
            $collection = $conn->subjects;
            $collection->insertOne($document);
            return true;
        } catch (Exception $e) {
            // Log or handle MongoDB insert failure
            error_log("Error inserting subject: " . $e->getMessage());
            return false;
        }
    }

    public static function enrollStudent($students, $semester, $batch, $section, $year)
    {
        $successCount = 0;
        $failureCount = 0;

        $enroll_result = [];
        if (!is_array($students) || empty($students)) {
            return ['error' => 'Invalid students data'];
        }

        // Process each student and their subjects

        foreach ($students as $reg_no => $student_data) {
            $subjects = $student_data['subjects'];
            $successSubjects = [];
            $failedSubjects = [];
            $updatedSubjects = [];

            foreach ($subjects as $subject) {
                // Insert into the database or process further
                $reg_no = (string) $reg_no;
                $result = Creator::enroll_Student($reg_no, $semester, $batch, $subject, $section, $year);

                if ($result == 'Enrolled') {
                    $successSubjects[] = $subject;
                } else if ($result == 'Updated') {
                    $updatedSubjects[] = $subject;
                } else if ($result == 'No Changes') {
                    $successSubjects[] = $subject; // Assuming "No Changes" is treated as a success
                } else {
                    $failedSubjects[] = $subject;
                }
            }

            // Append detailed enrollment results for each student
            $enroll_result[] = [
                'student_id' => $reg_no,
                'status' => [
                    'success' => count($successSubjects),
                    'failure' => count($failedSubjects),
                    'updated' => count($updatedSubjects),
                ],
                'details' => [
                    'success_subjects' => $successSubjects,
                    'failed_subjects' => $failedSubjects,
                    'updated_subjects' => $updatedSubjects,
                ],
            ];
        }

        return $enroll_result;
    }

    public static function enroll_Student($studentId, $semester, $batch, $subjectCode, $section, $year)
    {
        // error_log("Enrolling student: $studentId, Semester: $semester, Batch: $batch, Subject: $subjectCode, Section: $section, Year: $year");
        // note : student_id is the register number
        try {
            // Validate inputs
            if (empty($studentId) || empty($semester) || empty($batch) || empty($subjectCode) || empty($section) || empty($year)) {
                throw new Exception('All input parameters are required.');
            }

            // // Ensure that the input types are correct (e.g., studentId should be a string, semester and year should be integers)
            // if (!is_string($studentId) || !is_string($semester) || !is_string($batch) || !is_string($subjectCode) || !is_string($section) || !is_numeric($year)) {
            //     throw new Exception('Invalid input types.');
            // }

            // Database connection
            $conn = Database::getConnection();
            $collection = $conn->enrollments;  // Collection where enrollments are stored
            error_log("enroll_Student:: Connected to the database.");
            // Create an enrollment record
            $enrollment = [
                'student_id'   => $studentId, //here student_id is the register number
                'semester'     => $semester,
                'batch'        => $batch,
                'subject_code' => $subjectCode,
                'section'      => $section,
                'year'         => $year,
                'created_at'   => (new MongoDB\BSON\UTCDateTime())->toDateTime()->format('Y-m-d H:i:s')  // Current timestamp
            ];

            // Check if the student is already enrolled in the same subject for the same semester and section
            $existingEnrollment = $collection->findOne([
                'student_id'   => $studentId,
                'semester'     => $semester,
                'batch'        => $batch,
                'subject_code' => $subjectCode,
                'section'      => $section
            ]);

            if ($existingEnrollment) {
                // Update the existing enrollment if needed
                // error_log("enroll_Student:: Already Enrolled, Updating the existing enrollment.");
                $updateResult = $collection->updateOne(
                    ['_id' => $existingEnrollment['_id']],  // Find the existing record by its _id
                    ['$set' => [
                        'year' => $year,
                        'created_at' => (new MongoDB\BSON\UTCDateTime())->toDateTime()->format('Y-m-d H:i:s')  // Current timestamp
                    ]]
                );

                if ($updateResult->getModifiedCount() > 0) {
                    return 'Updated';
                } else {
                    error_log('enroll_Student::, Already Enrolled, No changes were made to the existing enrollment.');
                    return  'No Changes';
                }
            } else {
                error_log("enroll_Student:: New Enrollment, Inserting a new enrollment record.");
                // Insert the new enrollment record if it doesn't exist
                $insertResult = $collection->insertOne($enrollment);

                if ($insertResult->getInsertedCount() > 0) {
                    return  'Enrolled';
                } else {
                    error_log('enroll_Student:: Failed to enroll the student.');
                    return 'Failed';
                }
            }
        } catch (MongoDB\Driver\Exception\Exception $e) {
            // Catch MongoDB-specific errors
            throw new Exception("MongoDB Error: " . $e->getMessage());
        } catch (Exception $e) {
            // Catch general errors
            throw new Exception("Error: " . $e->getMessage());
        }
    }


    public static function assignFaculty(
        string $faculty_id,
        string $subject_code,
        string $batch,
        string $department,
        string $semester,
        string $section,
        array $student_sections,
        string $year
    ) {
        try {

            $conn = Database::getConnection();

            $collection = $conn->classes;

            // Validate required fields
            if (empty($faculty_id) || empty($subject_code) || empty($batch) || empty($department) || empty($semester) || empty($section) || empty($student_sections) || empty($year)) {
                throw new Exception("All fields are required.");
            }

            // Check if the same subject with the same class and students is already assigned
            $existing = $collection->findOne([
                'subject_code' => $subject_code,
                'batch' => $batch,
                'department' => $department,
                'semester' => $semester,
                'section' => $section,
                'student_sections' => $student_sections,
                'year' => $year
            ]);

            if ($existing) {
                return "The subject '$subject_code' for class '$section' with the same students is already assigned to faculty '$existing->faculty_id'.";
            }

            // Prepare data for insertion
            $insertData = [
                'faculty_id' => $faculty_id,
                'subject_code' => $subject_code,
                'batch' => $batch,
                'department' => $department,
                'semester' => $semester,
                'section' => $section,
                'student_sections' => $student_sections,
                'year' => $year,
                'status' => 'active',  // Set the initial status
                'created_at' => Date('Y-m-d H:i:s')
            ];

            // Insert data into the collection
            $result = $collection->insertOne($insertData);

            if($result->getInsertedCount() === 1) {
                return "Faculty Assigned successfully";
            } else {
                throw new Exception("Faculty assignment failed.");
            }

            // return "Faculty Assigned successfully";
        } catch (Exception $e) {
            error_log("Error uploading Assigning Faculty: " . $e->getMessage());
            return false;
        }
    }

    public static function createTest($testname, $month, $batch, $semester, $year, $department, $subjects, $duration, $totalmarks, $passmarks, $instructions)
    {
        try {
            // Get MongoDB connection
            $conn = Database::getConnection();
            $collection = $conn->tests;

            // Validate required fields
            if (empty($testname) || empty($month) || empty($batch) || empty($semester) || empty($year) || empty($department) || empty($subjects) || empty($duration) || empty($totalmarks) || empty($passmarks) || empty($instructions)) {
                throw new Exception("All fields are required.");
            }

            // TODO: Validate subjects array
            // 
            // if (!is_array($subjects) || empty($subjects)) {
            //     throw new Exception("Subjects must be an array and cannot be empty.");
            // }

            // // Check if each subject contains a valid subject code and date
            // foreach ($subjects as $subject) {
            //     if (empty($subject['subject_code']) || empty($subject['date'])) {
            //         throw new Exception("Each subject must have a code and a date.");
            //     }
            // }

            // Check if test already exists
            $old = $collection->findOne([
                'testname' => $testname,
                'month' => $month,
                'batch' => $batch,
                'semester' => $semester,
                'year' => $year,
                'department' => $department
            ]);

            if ($old) {
                return "duplicate";
            }
            $date = new DateTime();
            $date = $date->format('Y-m-d H:i:s');
            // Create the test document
            $test = [
                'testname' => $testname,
                'month' => $month,
                'batch' => $batch,
                'semester' => $semester,
                'year' => $year,
                'department' => $department,
                'subjects' => $subjects, // Array of subject codes and dates
                'duration' => $duration,
                'totalmarks' => $totalmarks,
                'passmarks' => $passmarks,
                'instructions' => $instructions,
                'status' => 'active', // Set the initial status
                'created_at' => $date// Store the current timestamp
            ];

            // Insert the document into the collection
            $result = $collection->insertOne($test);

            // Check the insertion result
            if ($result->getInsertedCount() === 1) {
                return true;
            } else {
                throw new Exception("Test creation failed.");
            }
        } catch (Exception $e) {
            // error_log("Error creating test: " . $e->getMessage());
            // return false; // Return the error message for debugging
            throw new Exception($e->getMessage());
        }
    }
}