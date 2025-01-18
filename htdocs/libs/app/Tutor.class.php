<?php

class Tutor
{

    private $conn;
    private $tutor_id;

    public function __construct($tutor_id)
    {
        $this->conn = Database::getConnection();

        if (!$tutor_id) {
            throw new Exception('Tutor ID is required.');
        }

        if (!Tutor::verify($tutor_id)) {
            error_log("Tutor '$tutor_id' not found.");
            throw new Exception("Tutor '$tutor_id' not found.");
        }

        $this->tutor_id = $tutor_id;
    }

    public function getTutorId()
    {
        return $this->tutor_id;
    }

    public static function verify($faculty_id)
    {
        $tutorsCollection = Database::getConnection()->tutors;
        $tutor = $tutorsCollection->findOne(['faculty_id' => $faculty_id]);

        return $tutor ? true : false;
    }

    public function getAssingedClass()
    {
        $tutorsCollection = $this->conn->tutors;

        $tutor = $tutorsCollection->findOne(["faculty_id" => $this->tutor_id], [
            'projection' => [
                '_id' => 0,
                'department' => 1,
                'batch' => 1,
                'section' => 1
            ]
        ]);

        if (!$tutor) {
            error_log("Tutor '$this->tutor_id' not found.");
            throw new Exception("Tutor '$this->tutor_id' not found.");
        }

        //convert object to array
        $tutor = iterator_to_array($tutor);

        return $tutor;
    }

    public function getTutorshipStudents()
    {
        $tutorsCollection = $this->conn->tutors;
        $studentsCollection = $this->conn->students;

        $tutor = $tutorsCollection->findOne(["faculty_id" => $this->tutor_id], [
            'projection' => [
                '_id' => 0,
                'department' => 1,
                'batch' => 1,
                'section' => 1
            ]
        ]);

        if (!$tutor) {
            error_log("Tutor '$this->tutor_id' not found.");
            throw new Exception("Tutor '$this->tutor_id' not found.");
        }

        //convert object to array
        $tutor = iterator_to_array($tutor);

        $students = $studentsCollection->find([
            "department" => $tutor['department'],
            "batch" => $tutor['batch'],
            "section" => $tutor['section']
        ], [
            'projection' => [
                '_id' => 0,
                'reg_no' => 1,
                'roll_no' => 1,
                'name' => 1,
                'department' => 1,
                'batch' => 1,
                'section' => 1
            ]
        ]);

        $students = iterator_to_array($students);

        return $students;
    }


    public static function assignTutor($faculty_id, $department, $batch, $section)
    {
        $tutorsCollection = Database::getConnection()->tutors;
        $yearinchargeCollection = Database::getConnection()->yearincharge;

        // Check if the faculty exists
        if (!Faculty::verify($faculty_id)) {
            error_log("Faculty '$faculty_id' not found.");
            throw new Exception("Faculty '$faculty_id' not found.");
        }

        // Check if the tutor already exists
        $tutor = $tutorsCollection->findOne(["faculty_id" => $faculty_id]);

        if ($tutor) {
            error_log("Tutor '$faculty_id' already assigned to department : $tutor->department batch : $tutor->batch and section $tutor->section");
            throw new Exception("Tutor '$faculty_id' already assigned to department : $tutor->department batch : $tutor->batch and section $tutor->section");
        }


        // check if faculty is assigned as year in charge

        $yearInCharge = $yearinchargeCollection->findOne(["faculty_id" => $faculty_id]);

        if ($yearInCharge) {
            error_log("Faculty '$faculty_id' is already assigned as year in charge for department : $yearInCharge->department and batch : $yearInCharge->batch");
            throw new Exception("Faculty '$faculty_id' is already assigned as year in charge for department : $yearInCharge->department and batch : $yearInCharge->batch");
        }


        $classCount = $tutorsCollection->countDocuments(["department" => $department, "batch" => $batch, "section" => $section]);

        if ($classCount >= 2) {
            error_log("Class already assigned to two tutors");
            throw new Exception("Class already assigned to two tutors");
        }


        $result = $tutorsCollection->insertOne([
            "faculty_id" => $faculty_id,
            "department" => $department,
            "batch" => $batch,
            "section" => $section
        ]);

        if ($result->getInsertedId()) {
            return $result->getInsertedId();
        } else {
            throw new Exception("Failed to assign tutor");
        }
    }

    public static function removeTutor($faculty_id)
    {
        $tutorsCollection = Database::getConnection()->tutors;

        $tutor = $tutorsCollection->findOne(["faculty_id" => $faculty_id]);

        if (!$tutor) {
            error_log("Tutor '$faculty_id' not found.");
            throw new Exception("Tutor '$faculty_id' not found.");
        }

        $result = $tutorsCollection->deleteOne(["faculty_id" => $faculty_id]);

        return $result->getDeletedCount();
    }

    public static function getTutors()
    {
        $tutorsCollection = Database::getConnection()->tutors;

        $tutors = $tutorsCollection->find([], [
            'projection' => [
                '_id' => 0,
                'faculty_id' => 1,
                'department' => 1,
                'batch' => 1,
                'section' => 1
            ]
        ]);

        $tutors = iterator_to_array($tutors);

        foreach ($tutors as $key => $tutor) {
            $tutors[$key] = (array)$tutor;
            $tutors[$key]['faculty_name'] = Faculty::getFacultyName($tutor['faculty_id']);
        }

        return $tutors;
    }

    public static function changeClass($faculty_id, $department, $batch, $section)
    {
        $tutorsCollection = Database::getConnection()->tutors;

        // Check if the faculty exists
        if (!Faculty::verify($faculty_id)) {
            error_log("Faculty '$faculty_id' not found.");
            throw new Exception("Faculty '$faculty_id' not found.");
        }

        // Check if the tutor already exists
        $tutor = $tutorsCollection->findOne(["faculty_id" => $faculty_id]);

        if (!$tutor) {
            error_log("Tutor '$faculty_id' not found to change.");
            throw new Exception("Tutor '$faculty_id' not found to change.");
        }

        $result = $tutorsCollection->updateOne(
            ["faculty_id" => $faculty_id],
            ['$set' => [
                "department" => $department,
                "batch" => $batch,
                "section" => $section
            ]]
        );

        if ($result->getModifiedCount()) {
            return $result->getModifiedCount();
        } else {
            throw new Exception("Failed to change tutor");
        }
    }

    public static function changeTutor($faculty_id, $new_faculty_id, $department, $batch, $section)
    {

        $tutorsCollection = Database::getConnection()->tutors;

        // Check if the faculty exists
        if (!Faculty::verify($faculty_id)) {
            error_log("Faculty '$faculty_id' not found.");
            throw new Exception("Faculty '$faculty_id' not found.");
        }

        // Check if the new faculty exists
        if (!Faculty::verify($new_faculty_id)) {
            error_log("Faculty '$new_faculty_id' not found.");
            throw new Exception("Faculty '$new_faculty_id' not found.");
        }

        // Check if the tutor already exists
        $tutor = $tutorsCollection->findOne(["faculty_id" => $faculty_id]);

        if (!$tutor) {
            error_log("Tutor '$faculty_id' not found to change.");
            throw new Exception("Tutor '$faculty_id' not found
            to change.");
        }

        $result = $tutorsCollection->updateOne(
            [
                "faculty_id" => $faculty_id,
                "department" => $department,
                "batch" => $batch,
                "section" => $section
            ],
            ['$set' => [
                "faculty_id" => $new_faculty_id
            ]]
        );

        if ($result->getModifiedCount()) {
            return $result->getModifiedCount();
        } else {
            throw new Exception("Failed to change tutor");
        }
    }
}