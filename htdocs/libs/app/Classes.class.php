<?php

class Classes
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }


    public function getClassDetails($class_id)
    {
        $class = $this->conn->classes->findOne(['_id' => new MongoDB\BSON\ObjectId($class_id)]);

        return $class;
    }


    /**
     * Fetch class details for a specific subject.
     */
    public static function getClass($subject_code, $faculty_id, $batch, $semester)
    {
        $collection = Database::getConnection()->classes;

       if(!$faculty_id) {
            throw new Exception('Faculty ID is required.');
        }

        if(!$subject_code) {
            throw new Exception('Subject code is required.');
        }

        if(!$batch) {
            throw new Exception('Batch is required.');
        }

        if(!$semester) {
            throw new Exception('Semester is required.');
        }

        try {
            $cursor = $collection->findOne(
                ['faculty_id' => $faculty_id, 'subject_code' => $subject_code, 'batch' => $batch, 'semester' => $semester],
                ['projection' => ['_id' => 0]]
            );

            if($cursor) {
                $result = iterator_to_array($cursor);
            } else {
                $result = false;
                throw new Exception('Record not found.');
            }
            
            return $result ?: false;
        } catch (Exception $e) {
            error_log('Error fetching classes: ' . $e->getMessage());
            return false;
        }
    }


    public static function getClassId($batch, $semester, $subject_code, $section, $department, $faculty_id)
    {
        $collection = Database::getConnection()->classes;

        try {
            $cursor = $collection->findOne(
                [
                    'faculty_id' => $faculty_id,
                    'batch' => $batch,
                    'semester' => $semester,
                    'subject_code' => $subject_code,
                    'section' => $section,
                    'department' => $department
                ],
                ['projection' => ['_id' => 1]]
            );

            if ($cursor) {
                $result = (string)$cursor->_id;
            } else {
                $result = false;
            }

            return $result;
        } catch (Exception $e) {
            error_log('Error fetching classes: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch classes handled by the faculty.
     */
    public static function getClasses($faculty_id)
    {
        $collection = Database::getConnection()->classes;

        try {
            $cursor = $collection->find(
                ['faculty_id' => $faculty_id],
                ['projection' => ['_id' => 0]]
            );

            return $cursor->toArray();
        } catch (Exception $e) {
            error_log('Error fetching classes: ' . $e->getMessage());
            return false;
        }
    }


}


