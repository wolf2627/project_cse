<?php

use MongoDB\Model\BSONArray;

class Faculty
{

    private $faculty_id;
    private $conn;

    public function __construct($facultyId = null)
    {

        $this->conn = Database::getConnection();
        if ($facultyId !== null) {

            // check for faculty id in the database
            $faculty = $this->conn->faculties->findOne(['faculty_id' => $facultyId]);

            if (!$faculty) {
                throw new Exception('Faculty not found.');
            }

            $this->faculty_id = $facultyId;
        } else {
            $this->faculty_id = Session::getUser()->getFacultyId();
        }
        $this->conn = Database::getConnection();
    }


    public static function getAllFaculties($key=null, $value=null)
    {
        $facultyCollection = Database::getConnection()->faculties;
        $faculties = $facultyCollection->find(
            $key && $value ? [$key => $value] : [],
            ['projection' => ['created_at' => 0]]
        );
        $result = [];

        foreach ($faculties as $faculty) {
            $result[] = [
                'faculty_id' => $faculty['faculty_id'],
                'name' => $faculty['name'],
                'email' => $faculty['email'],
                'department' => $faculty['department'],
                'designation' => $faculty['designation'],
                'role' => $faculty['role']
            ];
        }

        return $result;
    }


    /**
     * Verify if a faculty exists.
     * @param string $facultyId
     */
    public static function verify($facultyId)
    {
        $facultyCollection = Database::getConnection()->faculties;
        $faculty = $facultyCollection->findOne(['faculty_id' => $facultyId]);

        return $faculty ? true : false;
    }

    public function getFacultyId()
    {
        return $this->faculty_id;
    }

    public static function getFacultyName($faculty_id)
    {
        $facultyCollection = Database::getConnection()->faculties;
        $faculty = $facultyCollection->findOne(['faculty_id' => $faculty_id]);
        return $faculty ? $faculty['name'] : 'Unknown Faculty';
    }


    public function getFacultyDetails($facultyId)
    {
        $facultyCollection = $this->conn->faculties;
        $faculty = $facultyCollection->findOne(
            ['faculty_id' => $facultyId],
            ['projection' => ['created_at' => 0]]
        );

        if (!$faculty) {
            throw new Exception('Faculty not found.');
        }

        $result = iterator_to_array($faculty);

        return $result;
    }

    /**
     * Fetch batches assigned to the faculty.
     */
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
    public function getAssignedStudents($subjectCode, $batch, $semester, $section, $department, $facultyId = null)
    {
        $classCollection = $this->conn->classes;
        $enrollmentCollection = $this->conn->enrollments;
        $studentCollection = $this->conn->students;


        if (!$facultyId) {
            $facultyId = $this->faculty_id;
        }

        try {
            $class = $classCollection->findOne([
                'faculty_id' => $facultyId,
                'subject_code' => $subjectCode,
                'batch' => $batch,
                'semester' => $semester,
                'section' => $section,
                'department' => $department
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



    public function getSections($subjectCode, $batch, $semester, $faculty_id = false)
    {
        $classCollection = $this->conn->classes;

        if (!$faculty_id) {
            $faculty_id = $this->faculty_id;
        }

        try {
            $cursor = $classCollection->distinct('section', [
                'faculty_id' => $faculty_id,
                'subject_code' => $subjectCode,
                'batch' => $batch,
                'semester' => $semester
            ]);

            $sections = array_map(fn($section) => ['section' => $section], $cursor);

            //convert to single array
            $sections = array_column($sections, 'section');

            return $sections ?: false;
        } catch (Exception $e) {
            error_log('Error fetching sections: ' . $e->getMessage());
            return false;
        }
    }
}
