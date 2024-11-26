<?php

class Marks {

    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();        
    }

    public static function getMarks($batch, $semester, $subject_code, $testname, $section, $department, $faculty_id)
    {
        $collection = Database::getConnection()->marks;

        try {
            // Query the collection to fetch marks based on the criteria

            // TODO: Try to optimize this query to reduce the number of queries (use aggregation)

            $classId = Classes::getClassId($batch, $semester, $subject_code, $section, $department, $faculty_id);
            $testId = Test::getTestId($testname, $batch, $semester, $subject_code, $department);

            if(!$classId || !$testId) {
                return [];
            }

            $existingData = [
                'class_id' => new MongoDB\BSON\ObjectId($classId),
                'test_id' => new MongoDB\BSON\ObjectId($testId),
            ];

            $existing = $collection->findOne($existingData);

            if (!$existing) {
                return [];
            }

            return $existing;
        } catch (Exception $e) {
            // Log the error with more details for troubleshooting
            error_log('Error fetching marks: ' . $e->getMessage());

            return false;
        }
    }

    public static function updateMarks($reg_no, $new_mark, $batch, $semester, $subject_code, $testname, $section, $department, $faculty_id)
    {
        $collection = Database::getConnection()->marks;

        try {
            // Find the document with the given criteria

            $classId = Classes::getClassId($batch, $semester, $subject_code, $section, $department, $faculty_id);
            $testId = Test::getTestId($testname, $batch, $semester, $subject_code, $department);

            if(!$classId || !$testId) {
                throw new Exception('Record not found.');
            }

            $existingData = [
                'class_id' => new MongoDB\BSON\ObjectId($classId),
                'test_id' => new MongoDB\BSON\ObjectId($testId),
            ];

            $existing = $collection->findOne($existingData);

            if (!$existing) {
                throw new Exception('Marks record not found.');
            }

            // Update the marks for the student with the given reg_no

            $result = $collection->updateOne(
                [
                    'class_id' => new MongoDB\BSON\ObjectId($classId),
                    'test_id' => new MongoDB\BSON\ObjectId($testId),
                    'marks.reg_no' => $reg_no
                ],
                [
                    '$set' => [
                        'marks.$.marks' => $new_mark
                    ]
                ]
            );

            // Return true if one document was modified
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            // Log and throw the error
            error_log('Error updating marks: ' . $e->getMessage());
            throw new Exception('Error updating marks: ' . $e->getMessage());
        }
    }



    public static function enterMark($batch, $semester, $subject_code, $testname, $section, $marks, $department, $faculty_id)
    {
        $collection = Database::getConnection()->marks;

        foreach ($marks as &$mark) {
            $mark['marks'] = (int)$mark['marks'];
        }
        
        if(isset($batch) && isset($semester) && isset($subject_code) && isset($testname) && isset($section) && isset($department) && isset($faculty_id)) {
            error_log('All parameters are set');
        } else {
            error_log('All parameters are not set');
        }

        try {
            // Check if a record for this test already exists

            if(!$marks) {
                throw new Exception('No marks found.');
            }

            $classId = Classes::getClassId($batch, $semester, $subject_code, $section, $department, $faculty_id);
            $testId = Test::getTestId($testname, $batch, $semester, $subject_code, $department);

           
            if(!$testId) {
                throw new Exception('TestId not found.');
            }

            if(!$classId) {
                throw new Exception('ClassId not found.');
            }

            $existingData = [
                'class_id' => new MongoDB\BSON\ObjectId($classId),
                'test_id' => new MongoDB\BSON\ObjectId($testId),
            ];


            $existing = $collection->findOne($existingData);

            if ($existing) {
                return 'duplicate';
            }

            error_log('Entering marks for ' . count($marks) . ' students. No Duplicate found');

            // Transform marks into an array of structured objects
            $structuredMarks = [];
            foreach ($marks as $key => $student) {
                $structuredMarks[] = [
                    'reg_no' => $student['reg_no'],
                    'studentname' => $student['studentname'],
                    'marks' => $student['marks']
                ];
            }

            // Prepare the document to be inserted

            $finaldata = [
                'test_id' => new MongoDB\BSON\ObjectId($testId), //converting to object id
                'class_id' =>  new MongoDB\BSON\ObjectId($classId), //converting to object id
                'marks' => $structuredMarks,
                'Entered_at' => date('Y-m-d H:i:s')
            ];

            error_log('Final data: ' . json_encode($finaldata));

            // Insert the document into the collection
            $collection->insertOne($finaldata);

            return true;
        } catch (Exception $e) {
            error_log('Error entering marks: ' . $e->getMessage());
            return false;
        }
    }
}