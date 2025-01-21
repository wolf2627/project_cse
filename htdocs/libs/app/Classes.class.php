<?php

class Classes
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }


    // Verify if a class exists.

    public static function verify($class_id)
    {
        $classCollection = Database::getConnection()->classes;
        $class = $classCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($class_id)]);

        return $class ? true : false;
    }


    public function getClassDetails($class_id)
    {
        $class = $this->conn->classes->findOne(['_id' => new MongoDB\BSON\ObjectId($class_id)]);

        if(!$class) {
            throw new Exception('Class not found.');
        }

        $classArray = iterator_to_array($class);
        
        $result = Essentials::convertArray($classArray);
        
        return $result;
    }


    /**
     * Fetch class details for a specific subject.
     */
    public static function getClass($subject_code, $faculty_id, $batch, $semester, $status = 'active')
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
                ['faculty_id' => $faculty_id, 'subject_code' => $subject_code, 'batch' => $batch, 'semester' => $semester, 'status' => $status],
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


    public static function getClassId($batch, $semester, $subject_code, $section, $department, $faculty_id, $status = 'active')
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
                    'department' => $department,
                    'status' => $status
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
    public static function getClasses($faculty_id, $status = 'active')
    {
        $collection = Database::getConnection()->classes;

        try {
            $cursor = $collection->find(
            ['faculty_id' => $faculty_id, 'status' => $status]
            );

            $result = [];
            foreach ($cursor as $document) {
                $documentArray = iterator_to_array($document);
                if (isset($documentArray['_id'])) {
                    $documentArray['class_id'] = (string)$documentArray['_id'];
                    unset($documentArray['_id']);
                }
                if (isset($documentArray['student_sections']) && $documentArray['student_sections'] instanceof MongoDB\Model\BSONArray) {
                    $documentArray['student_sections'] = $documentArray['student_sections']->getArrayCopy();
                }
                $result[] = $documentArray;
            }

            return $result;
        } catch (Exception $e) {
            error_log('Error fetching classes: ' . $e->getMessage());
            return false;
        }
    }

    public static function getFaculties($subject_code, $batch, $status = 'active'){
        $collection = Database::getConnection()->classes;

        try {
            $cursor = $collection->distinct('faculty_id', ['subject_code' => $subject_code, 'batch' => $batch, 'status' => $status]);

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

    public static function getBatch($subject_code, $status = 'active'){
        $collection = Database::getConnection()->classes;

        try {
            $cursor = $collection->distinct('batch', ['subject_code' => $subject_code, 'status' => $status]);

            return $cursor ?: false;
        } catch (Exception $e) {
            error_log('Error fetching batch: ' . $e->getMessage());
            return false;
        }
    }

    public static function getClassIdsForBatch($batch, $status="active"){
        $collection = Database::getConnection()->classes;

        try {
            $cursor = $collection->find(['batch' => $batch, 'status' => $status], ['projection' => ['_id' => 1]]);

            $result = [];
            foreach ($cursor as $document) {
                $result[] = (string)$document->_id;
            }

            if(!$result) {
                throw new Exception('No classes found.');
            }

            return $result;
        } catch (Exception $e) {
            error_log('Error fetching classes: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Fetch all available department, batch, section for students.
     */
    public function fetchStudentsClasses(){

        $studCollection = $this->conn->students;

        $cursor = $studCollection->aggregate([
            ['$group' => [
                '_id' => [
                    'department' => '$department',
                    'batch' => '$batch',
                    'section' => '$section'
                ]
            ]]
        ]);

        $result = [];

        foreach ($cursor as $document) {
            $result[] = $document['_id'];
        }

        return $result;

    }
}