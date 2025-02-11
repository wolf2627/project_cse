<?php

class Contest
{
    private $contestId;
    private $db;
    private $contest;
    private $collection;

    public function __construct($contestId) {
        
        $this->db = Database::getConnection();
        $this->collection = $this->db->contests;

        $contest = $this->db->contests->findOne(['_id' => new MongoDB\BSON\ObjectId($contestId)]);

        if (!$contest) {
            throw new Exception('Contest not found');
        }

        $this->contestId = $contestId;
        $this->contest = $contest;
    }


    public static function createContest($title, $description, $contestType, $rounds ,$startTime, $endTime, $registrationDeadline, $facultyId, $coordinators = []) {
    
        $collection = Database::getConnection()->contests;
    
        $contest = [
            "title" => $title,
            "description" => $description,
            "instructions" => "",
            "contest_type" => $contestType,
            "rounds" => [],
            "current_round" => 0,
            "created_by" => $facultyId,
            "start_time" => new MongoDB\BSON\UTCDateTime(strtotime($startTime) * 1000),
            "end_time" => new MongoDB\BSON\UTCDateTime(strtotime($endTime) * 1000),
            "registration_deadline" => new MongoDB\BSON\UTCDateTime(strtotime($registrationDeadline) * 1000),
            "registration_open" => true,
            "registered_users" => [],
            "participants" => [],
            "jury" => [],
            "coordinators" => $coordinators,
            "winners" => [],
            "status" => "upcoming",
            "created_at" => new MongoDB\BSON\UTCDateTime()
        ];
    
        $result = $collection->insertOne($contest);

        $insertedId = $result->getInsertedId();

        foreach ($rounds as $round) {
            $roundId = Contest::contestRound($insertedId, $round['title'], $round['round_number'], $round['start_time'], $round['end_time']);

            $roundIds[] = $roundId;
        }

        $collection->updateOne(['_id' => $insertedId], ['$set' => ['rounds' => $roundIds]]);

        return $insertedId;
    }


    public static function contestRound($contestId, $name, $roundNumber, $startTime, $endTime, $questions = [], $participants = [], $qualifiedUsers = []) {
        $collection = Database::getConnection()->contest_rounds;

        $round = [
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "name" => $name,
            "round_number" => $roundNumber,
            "start_time" => new MongoDB\BSON\UTCDateTime(strtotime($startTime) * 1000),
            "end_time" => new MongoDB\BSON\UTCDateTime(strtotime($endTime) * 1000),
            "questions" => array_map(function($id) { return new MongoDB\BSON\ObjectId($id); }, $questions),
            "participants" => array_map(function($id) { return new MongoDB\BSON\ObjectId($id); }, $participants),
            "qualified_users" => array_map(function($id) { return new MongoDB\BSON\ObjectId($id); }, $qualifiedUsers)
        ];

        return $collection->insertOne($round)->getInsertedId();
    }

    public function changeStatus($contestId, $status)
    {
        $collection = Database::getConnection()->contests;

        $contest = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($contestId)]);

        if (!$contest) {
            throw new Exception('Contest not found');
        }

        $result = $collection->updateOne(['_id' => new MongoDB\BSON\ObjectId($contestId)], ['$set' => ['status' => $status]]);

        return $result->getModifiedCount();
    }


    public function setJuries($juries) {
        // set juries for contest

        $result = $this->collection->updateOne(['_id' => new MongoDB\BSON\ObjectId($this->contestId)], ['$set' => ['jury' => $juries]]);

        return $result->getModifiedCount();
    }

    public function getJuries() {
        // get juries for contest
        return $this->contest['jury'];
    }

    public function setWinners() {}

    public function getWinners() {
        return $this->contest['winners'];
    }

}