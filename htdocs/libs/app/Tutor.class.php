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

        foreach ($students as $key => $student) {
            $students[$key] = iterator_to_array($student);
        }

        // Sort by reg_no 
        usort($students, function ($a, $b) {
            return $a['reg_no'] <=> $b['reg_no'];
        });

        return $students;
    }

    public static function assignTutor($faculty_id, $department, $batch, $section, $status = 'active')
    {
        $tutorsCollection = Database::getConnection()->tutors;


        if (!Faculty::verify($faculty_id)) {
            error_log("Faculty '$faculty_id' not found.");
            throw new Exception("Faculty '$faculty_id' not found.");
        }

        if (YearInCharge::verify($faculty_id)) {
            error_log("Tutor '$faculty_id' is already assigned as Year In Charge.");
            throw new Exception("Tutor '$faculty_id' is already assigned as Year In Charge.");
        }

        $tutor = $tutorsCollection->findOne(['faculty_id' => $faculty_id]);

        $exisitingClass = $tutorsCollection->findOne([
            "department" => $department,
            "batch" => $batch,
            "section" => $section,
            "status" => $status
        ]);

        if ($tutor) {
            $result = $tutorsCollection->updateOne(
                ['faculty_id' => $faculty_id],
                ['$set' => [
                    'department' => $department,
                    'batch' => $batch,
                    'section' => $section
                ]]
            );

            return $result->getModifiedCount() > 0 ? true : true;
        } else if ($exisitingClass) {

            $result = $tutorsCollection->updateOne(
                [
                    "department" => $department,
                    "batch" => $batch,
                    "section" => $section,
                    "status" => $status
                ],
                ['$set' => [
                    'faculty_id' => $faculty_id
                ]]
            );

            return $result->getModifiedCount() > 0 ? true : true;
        } else {
            $tutorsCollection->insertOne([
                'faculty_id' => $faculty_id,
                'department' => $department,
                'batch' => $batch,
                'section' => $section,
                'status' => $status
            ]);

            return true;
        }
    }


    public static function unassignTutor($faculty_id)
    {
        $tutorsCollection = Database::getConnection()->tutors;

        if (!Faculty::verify($faculty_id)) {
            error_log("Faculty '$faculty_id' not found.");
            throw new Exception("Faculty '$faculty_id' not found.");
        }

        $tutor = $tutorsCollection->findOne(['faculty_id' => $faculty_id]);

        if (!$tutor) {
            error_log("Tutor '$faculty_id' not found.");
            throw new Exception("Tutor '$faculty_id' not found.");
        }

        $result = $tutorsCollection->deleteOne(['faculty_id' => $faculty_id]);

        return $result->getDeletedCount() > 0;
    }

    public static function getTutors($batch = null, $section = null, $department = null)
    {
        $tutorsCollection = Database::getConnection()->tutors;

        $query = [];

        if ($batch) {
            $query['batch'] = $batch;
        }

        if ($section) {
            $query['section'] = $section;
        }

        if ($department) {
            $query['department'] = $department;
        }

        $tutors = $tutorsCollection->find($query, [
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
            $tutors[$key] = iterator_to_array($tutor);
            $tutors[$key]['faculty_name'] = Faculty::getFacultyName($tutor['faculty_id']);
        }

        // sort by batch and section
        usort($tutors, function ($a, $b) {
            if ($a['batch'] == $b['batch']) {
                return $a['section'] <=> $b['section'];
            }
            return $a['batch'] <=> $b['batch'];
        });

        return $tutors ? $tutors : false;
    }
}