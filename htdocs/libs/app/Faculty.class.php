<?php

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

    public function getClasses()
    {
        $collection = $this->conn->classes;

        $cursor = $collection->find(
            ['faculty_id' => $this->faculty_id],
            ['projection' => ['_id' => 0]]
        );

        $result = $cursor->toArray();

        return $result;
    }

    public function enterMark($batch, $semester, $subject_code, $marks, $testname, $section)
    {

        $conn = Database::getConnection();
        $collection = $conn->marks;

        $date = date('Y-m-d H:i:s');
        $data = [
            'faculty_id' => $this->faculty_id,
            'batch' => $batch,
            'semester' => $semester,
            'subject_code' => $subject_code,
            'marks' => $marks,
            'test_name' => $testname,
            'section' => $section,
            'created_at' => $date
        ];

        $existing = $collection->findOne([
            'faculty_id' => $this->faculty_id,
            'batch' => $batch,
            'semester' => $semester,
            'subject_code' => $subject_code,
            'test_name' => $testname,
            'section' => $section
        ]);

        if ($existing) {
            return 'duplicate';
        } else {
            $collection->insertOne($data);
            return  true;
        }
    }

    public function getAssignedStudents($facultyId, $subjectCode)
    {
        // Fetch the Class collection
        $classCollection = $this->conn->classes;
        $enrollmentCollection = $this->conn->enrollments;
        $studentCollection = $this->conn->students;

        try {
            // Step 1: Find the class details for the given faculty ID and subject code
            $class = $classCollection->findOne([
                'faculty_id' => $facultyId,
                'subject_code' => $subjectCode
            ]);


            if (!$class) {
                return ['error' => 'No class found for the given faculty ID and subject code.'];
            }

            // Step 2: Fetch student enrollments based on the class details
            $enrollments = $enrollmentCollection->find([
                'batch' => $class['batch'],
                'semester' => $class['semester'],
                'subject_code' => $class['subject_code'],
                'section' => ['$in' => $class['student_sections']],
                'year' => $class['year']
            ]);

            if ($enrollments->isDead()) {
                return ['error' => 'No students found in enrollment for the specified class.'];
            }

            // Collect student IDs from the enrollment results
            $studentIds = [];
            foreach ($enrollments as $enrollment) {
                $studentIds[] = $enrollment['student_id'];
            }

            // Step 3: Find student details in the Student collection
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

            if (empty($students)) {
                return ['error' => 'No student details found.'];
            }

            return $students;
        } catch (Exception $e) {
            return ['error' => 'An error occurred: ' . $e->getMessage()];
        }
    }
}
