<?php

use MongoDB\Model\BSONArray;

class Faculty
{

    private $faculty_id;
    private $conn;

    public function __construct()
    {
        $this->faculty_id = Session::getUser()->getFacultyId();
        $this->conn = Database::getConnection();
    }

    public function getFacultyId()
    {
        return $this->faculty_id;
    }

    public static function getFacultyName($faculty_id){
        $facultyCollection = Database::getConnection()->faculties;
        $faculty = $facultyCollection->findOne(['faculty_id' => $faculty_id]);
        return $faculty ? $faculty['name'] : 'Unknown Faculty';
    }


    public function getBatches()
    {
        $collection = $this->conn->classes;

        try {
            $cursor = $collection->distinct('batch', ['faculty_id' => $this->faculty_id]);

            $cursor = $cursor;

            $batches = array_map(fn($batch) => ['batch' => $batch], $cursor);
            return $cursor ?: false;
        } catch (Exception $e) {
            error_log('Error fetching batches: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch subjects assigned to the faculty.
     */
    public function getSubjects()
    {
        $collection = $this->conn->classes;

        try {
            $cursor = $collection->find(
                ['faculty_id' => $this->faculty_id],
                ['projection' => ['subject_code' => 1, 'batch' => 1, '_id' => 0]]
            );

            $result = $cursor->toArray();
            $subjectCodes = array_map(fn($item) => $item['subject_code'], $result);


            return $subjectCodes ?: false;
        } catch (Exception $e) {
            error_log('Error fetching subjects: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch tests assigned to the faculty's subjects.
     */
    public function getFacultyAssignedTests()
    {
        $testCollection = $this->conn->tests;

        try {
            $subjectCodes = $this->getSubjects();
            if (isset($subjectCodes['error'])) {
                throw new Exception($subjectCodes['error']);
            }

            $batches = $this->getBatches();
            if (isset($batches['error'])) {
                throw new Exception($batches['error']);
            }

            $cursor = $testCollection->find([
                'status' => 'active',
                'batch' => ['$in' => $batches],
                'subjects.subject_code' => ['$in' => $subjectCodes]
            ]);

            $result = [];
            foreach ($cursor as $test) {
                $testName = $test['testname'];

                // Convert BSONArray to a PHP array
                $subjectsArray = isset($test['subjects']) ? (array)$test['subjects'] : [];

                // Ensure $subjectsArray is a PHP array before applying array_filter
                if (is_array($subjectsArray)) {
                    $testSubjects = array_column(
                        array_filter(
                            $subjectsArray,
                            fn($subject) => in_array($subject['subject_code'], $subjectCodes)
                        ),
                        'subject_code'
                    );

                    // Add batch and semester details
                    $result[$testName] = [
                        'subjects' => $testSubjects,
                        'batches' => [$test['batch']],
                        'department' => $test['department'],
                        'maxmark' => $test['totalmarks'],
                        'passmark' => $test['passmarks'],
                        'semesters' => [$test['semester'] ?? 'Unknown'] // Add semester if available
                    ];
                }
            }

            return $result ?: throw new Exception('No tests found.');
        } catch (Exception $e) {
            //error_log('Error fetching tests: ' . $e->getMessage());
            return false;
        }
    }


    
    /**
     * Fetch students assigned to a faculty and subject.
     */
    public function getAssignedStudents($subjectCode, $batch, $semester, $facultyId = null)
    {
        $classCollection = $this->conn->classes;
        $enrollmentCollection = $this->conn->enrollments;
        $studentCollection = $this->conn->students;

        
        if(!$facultyId) {
            $facultyId = $this->faculty_id;
        }

        try {
            $class = $classCollection->findOne([
                'faculty_id' => $facultyId,
                'subject_code' => $subjectCode,
                'batch' => $batch,
                'semester' => $semester
            ]);

            if (!$class) {
                throw new Exception('Class not found.');
            }

            $enrollments = $enrollmentCollection->find([
                'batch' => $class['batch'],
                'semester' => $class['semester'],
                'subject_code' => $class['subject_code'],
                'section' => ['$in' => $class['student_sections']],
                'year' => $class['year']
            ], [
                'sort' => ['student_id' => 1]
            ]);

            $studentIds = array_map(fn($enrollment) => $enrollment['student_id'], iterator_to_array($enrollments));

            $studentsCursor = $studentCollection->find([
                'reg_no' => ['$in' => $studentIds]
            ]);

            $students = [];
            foreach ($studentsCursor as $student) {
                $students[] = [
                    'name' => $student['name'],
                    'reg_no' => $student['reg_no']
                ];
            }

            return $students ?: throw new Exception('No students found.');
        } catch (Exception $e) {
            error_log('Error fetching students: ' . $e->getMessage());
            return false;
        }
    }
}
