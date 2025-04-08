<?php

class Student
{

    private $conn;
    private $student_id;

    public function __construct($student_id)
    {
        $this->conn = Database::getConnection();

        // If a student ID is provided, fetch the student details
        if ($student_id) {
            $student = $this->conn->students->findOne(['reg_no' => $student_id]);

            if (!$student) {
                throw new Exception('Student not found.');
            }
        }

        $this->student_id = $student_id;

    }

    public static function verify($student_id)
    {
        $conn = Database::getConnection();

        $student = $conn->students->findOne([
            '$or' => [
            ['reg_no' => $student_id],
            ['roll_no' => $student_id]
            ]
        ]);

        if (!$student) {
            throw new Exception('Student not found.');
        }

        return true;
    }

    public function getStudentDetails()
    {

        $search_param = strtoupper($this->student_id);

        $student = $this->conn->students->findOne([
            '$or' => [
                ['reg_no' => $search_param],
                ['roll_no' => $search_param]
            ]
        ], ['projection' => ['created_at' => 0, '_id' => 0]]);

        if (!$student) {
            throw new Exception('Student not found.');
        }
        $result = iterator_to_array($student);

        return $result;
    }

    public function getEnrollments()
    {
        $student_id = $this->student_id;

        $enrolled_classes = $this->conn->enrollments->find(
            ['student_id' => $student_id],
            ['projection' => ['created_at' => 0]]
        )->toArray();

        if (!$enrolled_classes) {
            throw new Exception('No classes found.');
        }

        $result = [];
        foreach ($enrolled_classes as $class) {
            $result[] = [
                '_id' => (string) $class['_id'], // Convert ObjectId to string
                'student_id' => $class['student_id'],
                'semester' => $class['semester'],
                'batch' => $class['batch'],
                'subject_code' => $class['subject_code'],
                'section' => $class['section'],
                'year' => $class['year'],
            ];
        }

        error_log(json_encode($result));

        return $result;
    }

    public function getEnrolledClasses($status = 'active')
    {
        // Default to the logged-in user's registration number if no student_id is provided
        $student_id = $this->student_id;

    
        // Find all enrollments for the student
        $enrolled = $this->getEnrollments();

        $result = [];

        // Iterate through each enrollment and fetch corresponding class details
        foreach ($enrolled as $class) {
            $class_details = $this->conn->classes->findOne([
                'subject_code' => $class['subject_code'],
                'semester' => $class['semester'],
                'batch' => $class['batch'],
                //'section' => $class['section'],
                'student_sections' => $class['section'],
                'year' => $class['year'],
                'status' => $status
            ]);

            if ($class_details) {
                $result[] = [
                    'class_id' => (string) $class_details['_id'],
                    'faculty' => Faculty::getFacultyName($class_details['faculty_id']),
                    'subject_code' => $class_details['subject_code'],
                    'department' => $class_details['department'],
                    'semester' => $class_details['semester'],
                    'batch' => $class_details['batch'],
                    'section' => $class_details['section'],
                ];
            } else {
                continue;
            }
        }

        return $result;
    }
}
