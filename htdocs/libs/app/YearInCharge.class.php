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

    public function getAssignedYearIncharges($faculty_id, $dapartment=null, $batch=null)
    {
        $yearInChargeCollection = $this->conn->yearincharge;

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

        $yearInCharges = $yearInChargeCollection->find(
            $query,
            ['projection' => ['_id' => 0]]
        );

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


        $existingYearInCharge = $yearInChargeCollection->findOne([
            "faculty_id" => $faculty_id,
        ]);


        if ($existingYearInCharge) {
            $result = $yearInChargeCollection->updateOne(
                ["faculty_id" => $faculty_id],
                ['$set' => [
                    "department" => $dapartment,
                    "batch" => $batch
                ]]
            );
        } else {
            $result = $yearInChargeCollection->insertOne([
                "faculty_id" => $faculty_id,
                "department" => $dapartment,
                "batch" => $batch
            ]);
        }

        if ($result->getModifiedCount() || $result->getInsertedId()) {
            return $result->getModifiedCount() ?: $result->getInsertedId();
        } else {
            throw new Exception("Failed to assign year in charge");
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
