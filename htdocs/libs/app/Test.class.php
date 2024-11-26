<?php

class Test {
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public static function getTestDetails($test_id)
    {
        $test = Database::getConnection()->tests->findOne(['_id' => new MongoDB\BSON\ObjectId($test_id)]);

        return $test;
    }


    public static function getTestId($testname, $batch, $semester, $subject_code, $department)
    {
        $collection = Database::getConnection()->tests;

        try {
            $cursor = $collection->findOne(
                [
                    'testname' => $testname,
                    'batch' => $batch,
                    'semester' => $semester,
                    'subjects.subject_code' => $subject_code,
                    'department' => $department
                ],
                ['projection' => ['_id' => 1]]
            );

            if ($cursor) {
                // Correctly access the _id field as a property
                $result = (string)$cursor->_id; // Convert ObjectId to string
            } else {
                $result = false; // Return false if no document is found
            }

            return $result;
        } catch (Exception $e) {
            error_log('Error fetching tests: ' . $e->getMessage());
            return false; // Return false if an exception occurs
        }
    }

    /** function to return tests available and its details */

    public static function getTests(){
        $collection = Database::getConnection()->tests;
        $cursor = $collection->find();
        $tests = [];
        foreach ($cursor as $test) {
            $test['_id'] = (string)$test['_id'];
            $tests[] = $test;
        }
        return $tests;
    }
}