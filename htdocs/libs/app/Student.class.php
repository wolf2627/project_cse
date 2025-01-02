<?php

class Student
{

    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }


    public function getStudentDetails($search_param)
    {

        $search_param = strtoupper($search_param);

        $student = $this->conn->students->findOne([
            '$or' => [
                ['reg_no' => $search_param],
                ['roll_no' => $search_param]
            ]
        ], ['projection' => ['created_at' => 0]]);

        if (!$student) {
            throw new Exception('Student not found.');
        }
        $result = iterator_to_array($student);

        return $result;
    }
}
