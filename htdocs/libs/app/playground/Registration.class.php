<?php

class ContestRegistration
{
    public static function registerForContest($contestId, $studentId)
    {
        $db = Database::getConnection();

        $collection = $db->registrations;

        $registration = [
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "student_id" => $studentId,
            "status" => "pending",
            "registered_at" => new MongoDB\BSON\UTCDateTime()
        ];

        return $collection->insertOne($registration)->getInsertedId();
    }

    public static function confirmRegistration($contestId, $studentId, $facultyId) {
        $db = Database::getConnection();
    
        $registrations = $db->registrations;
        $contests = $db->contests;
    
        $registrations->updateOne(
            ["contest_id" => new MongoDB\BSON\ObjectId($contestId), "student_id" => $studentId],
            ['$set' => ["status" => "approved", "confirmed_by" => $facultyId]]
        );
    
        $contests->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($contestId)],
            ['$push' => ["participants" => $studentId]]
        );
    
        return true;
    }
    
}