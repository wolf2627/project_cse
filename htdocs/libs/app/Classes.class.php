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
               // ['projection' => ['_id' => 0]]
               ['projection' => ['created_at' => 0]]
            );

            if($cursor) {
                $result = iterator_to_array($cursor);

                foreach ($result as $key => $value) {
                    if ($key == '_id') {
                        $result['class_id'] = (string)$value;
                        unset($result['_id']);
                    }
                }
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

    public static function getFaculties($subject_code, $batch){
        $collection = Database::getConnection()->classes;

        try {
            $cursor = $collection->distinct('faculty_id', ['subject_code' => $subject_code, 'batch' => $batch]);

            $faculties = array_map(fn($faculty_id) => ['faculty_id' => $faculty_id], $cursor);


            $facultyDetails = [];

            foreach ($faculties as $faculty) {
               // echo "Faculty ID: " . $faculty['faculty_id'] . "<br>";
                $facultyObj = new Faculty($faculty['faculty_id']); // For other user to get faculty details
                $facultyDetail = $facultyObj->getFacultyDetails($faculty['faculty_id']);
                $facultyDetails[] = [
                    'name' => $facultyDetail['name'],
                    'department' => $facultyDetail['department'],
                    'id' => $facultyDetail['faculty_id']
                ];
            }

            return $facultyDetails ?: false;

        } catch (Exception $e) {
            error_log('Error fetching faculties: ' . $e->getMessage());
            return false;
        }
    }

    public static function getBatch($subject_code){
        $collection = Database::getConnection()->classes;

        try {
            $cursor = $collection->distinct('batch', ['subject_code' => $subject_code]);

            return $cursor ?: false;
        } catch (Exception $e) {
            error_log('Error fetching batch: ' . $e->getMessage());
            return false;
        }
    }
}