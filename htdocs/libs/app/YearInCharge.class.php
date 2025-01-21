<?php

class YearInCharge
{
    private $conn;
    private $year_in_charge_id;

    public function __construct($year_in_charge_id)
    {
        $this->conn = Database::getConnection();

        if (!Faculty::verify($year_in_charge_id)) {
            error_log("Year in charge '$year_in_charge_id' not found.");
            throw new Exception("Year in charge '$year_in_charge_id' not found.");
        }

        $this->year_in_charge_id = $year_in_charge_id;
    }

    public function getYearInChargeId()
    {
        return $this->year_in_charge_id;
    }

    public static function verify($faculty_id)
    {
        $yearInChargeCollection = Database::getConnection()->yearincharge;
        $yearInCharge = $yearInChargeCollection->findOne(['faculty_id' => $faculty_id]);

        return $yearInCharge ? true : false;
    }

    public static function getAssignedYearIncharges($faculty_id = null, $dapartment = null, $batch = null)
    {
        $yearInChargeCollection = Database::getConnection()->yearincharge;

        $query = [];
        if ($faculty_id) {
            $query['faculty_id'] = $faculty_id;
        }
        if ($dapartment) {
            $query['department'] = $dapartment;
        }
        if ($batch) {
            $query['batch'] = $batch;
        }

        if (empty($query)) {
            $yearInCharges = $yearInChargeCollection->find([], ['projection' => ['_id' => 0]]);
        } else {
            $yearInCharges = $yearInChargeCollection->find(
                $query,
                ['projection' => ['_id' => 0]]
            );
        }


        $result = [];

        foreach ($yearInCharges as $yearInCharge) {
            $result[] = [
                'faculty_id' => $yearInCharge['faculty_id'],
                'department' => $yearInCharge['department'],
                'batch' => $yearInCharge['batch']
            ];
        }

        return $result;
    }


    public static function assignYearInCharge($faculty_id, $dapartment, $batch)
    {
        $yearInChargeCollection = Database::getConnection()->yearincharge;

        if (!$faculty_id) {
            throw new Exception("Faculty ID is required.");
        }

        if (!$dapartment) {
            throw new Exception("Department is required.");
        }

        if (!$batch) {
            throw new Exception("Batch is required.");
        }

        if (!Faculty::verify($faculty_id)) {
            throw new Exception("Faculty not found.");
        }

        $exisitingTutor = Database::getConnection()->tutors->findOne([
            "faculty_id" => $faculty_id
        ]);

        if ($exisitingTutor) {
            throw new Exception("Faculty is already a tutor.");
        }


        $existingYearInCharge = $yearInChargeCollection->findOne([
            "faculty_id" => $faculty_id,
        ]);

        $exitingYearBatch = $yearInChargeCollection->findOne([
            "department" => $dapartment,
            "batch" => $batch
        ]);


        if ($existingYearInCharge) {
            $result = $yearInChargeCollection->updateOne(
                ["faculty_id" => $faculty_id],
                ['$set' => [
                    "department" => $dapartment,
                    "batch" => $batch
                ]]
            );

            $return = $result->getModifiedCount();

            if ($return == 0) {
                throw new Exception("Already assigned to the same department and batch.");
            }
        } else if ($exitingYearBatch) {
            $result = $yearInChargeCollection->updateOne(
                ["department" => $dapartment, "batch" => $batch],
                ['$set' => [
                    "faculty_id" => $faculty_id
                ]]
            );

            $return = $result->getModifiedCount();

            if ($return == 0) {
                throw new Exception("Already assigned to the same department and batch.");
            }
        } else {
            $result = $yearInChargeCollection->insertOne([
                "faculty_id" => $faculty_id,
                "department" => $dapartment,
                "batch" => $batch
            ]);

            $return = $result->getInsertedId();
        }

        if ($return) {
            return $return;
        } else {
            throw new Exception("Assignment failed.");
        }
    }

    public static function removeYearInCharge($faculty_id)
    {
        $yearInChargeCollection = Database::getConnection()->yearincharge;

        if (!Faculty::verify($faculty_id)) {
            throw new Exception("Faculty not found.");
        }

        $existingYearInCharge = $yearInChargeCollection->findOne([
            "faculty_id" => $faculty_id,
        ]);

        if (!$existingYearInCharge) {
            throw new Exception("Year in charge not found.");
        }

        $result = $yearInChargeCollection->deleteOne(["faculty_id" => $faculty_id]);

        return $result->getDeletedCount();
    }
}
