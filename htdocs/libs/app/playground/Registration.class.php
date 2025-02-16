<?php

class ContestRegistration
{
    public static function registerForContest($contestId, $studentId)
    {
        $db = Database::getConnection();

        $collection = $db->registrations;

        $registered = $collection->findOne(["contest_id" => new MongoDB\BSON\ObjectId($contestId), "student_id" => $studentId]);

        if ($registered) {
            throw new Exception('Already registered for contest');
        }

        $registration = [
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "student_id" => $studentId,
            "status" => "pending",
            "registered_at" => new MongoDB\BSON\UTCDateTime()
        ];

        return $collection->insertOne($registration)->getInsertedId();
    }

    public static function confirmRegistration($contestId, $studentId, $facultyId)
    {
        $db = Database::getConnection();

        $registrations = $db->registrations;
        $contests = $db->contests;

        $confirmed = $registrations->findOne(["contest_id" => new MongoDB\BSON\ObjectId($contestId), "student_id" => $studentId, "status" => "approved"]);

        if ($confirmed) {
            throw new Exception('Already confirmed registration');
        }

        $registrations->updateOne(
            ["contest_id" => new MongoDB\BSON\ObjectId($contestId), "student_id" => $studentId],
            ['$set' => ["status" => "approved", "confirmed_by" => $facultyId]]
        );

        $contest = $contests->findOne(["_id" => new MongoDB\BSON\ObjectId($contestId), "participants" => $studentId]);

        if (!$contest) {
            $contests->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($contestId)],
            ['$push' => ["participants" => $studentId]]
            );
        }

        return true;
    }

    public static function showRegistrations($contestId, $status = "pending")
    {

        Contest::verfiyContestforRegistration($contestId);

        $db = Database::getConnection();

        $registrations = $db->registrations;

        $registrations = $registrations->find(["contest_id" => new MongoDB\BSON\ObjectId($contestId), "status" => $status]);

        $registrations = iterator_to_array($registrations);

        $registrations = array_map(function ($registration) {
            $registrationArray = $registration->getArrayCopy();
            $registrationArray['_id'] = (string) $registrationArray['_id'];
            $registrationArray['contest_id'] = (string) $registrationArray['contest_id'];
            return $registrationArray;
        }, $registrations);

        return $registrations ? $registrations : [];
    }

    public static function isRegistered($contestId, $studentId)
    {
        $db = Database::getConnection();

        $registrations = $db->registrations;

        $registered = $registrations->findOne(["contest_id" => new MongoDB\BSON\ObjectId($contestId), "student_id" => $studentId]);

        $registered = $registered ? $registered->getArrayCopy() : false;

        return $registered ? $registered['status'] : false;
    }
}
