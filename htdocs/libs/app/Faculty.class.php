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

    public function enterMark($batch, $semester, $subject, $marks, $testname,) {}
}
