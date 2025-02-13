<?php

class Contest
{
    private $contestId;
    private $db;
    private $contest;
    private $collection;

    public function __construct($contestId)
    {

        $this->db = Database::getConnection();
        $this->collection = $this->db->contests;

        $contest = $this->db->contests->findOne(['_id' => new MongoDB\BSON\ObjectId($contestId)]);

        if (!$contest) {
            throw new Exception('Contest not found');
        }

        $this->contestId = $contestId;
        $this->contest = $contest;
    }

    public static function verfiyContestforRegistration($contestId)
    {
        $db = Database::getConnection();

        $contest = $db->contests->findOne(['_id' => new MongoDB\BSON\ObjectId($contestId), 'registration_open' => true]);

        if (!$contest) {
            throw new Exception('Contest not found');
        }

        return $contest;
    }


    public static function showContests($status = 'upcoming', $registrationopen = null)
    {
        $db = Database::getConnection();

        $query = [];

        if ($status) {
            $query['status'] = $status;
        }

        if ($registrationopen) {
            $query['registration_open'] = $registrationopen;
        }


        // Find contests with sorting and exclude 'rounds' field
        $cursor = $db->contests->find(
            $query,
            [
                'sort' => ['start_time' => 1],
                //'projection' => ['rounds' => 0] // Exclude 'rounds'
            ]
        );

        // Convert MongoDB Cursor to an array
        $contests = iterator_to_array($cursor);

        // Helper function to convert BSON date fields
        $convertDate = fn($date) => $date instanceof MongoDB\BSON\UTCDateTime ? $date->toDateTime()->format('Y-m-d H:i:s') : null;

        foreach ($contests as &$contest) {
            $contest['_id'] = (string) $contest['_id'];
            $contest['start_time'] = $convertDate($contest['start_time']);
            $contest['end_time'] = $convertDate($contest['end_time']);
            $contest['registration_deadline'] = $convertDate($contest['registration_deadline']);
            $contest['created_at'] = $convertDate($contest['created_at']);
            $contest['registration_open'] = $contest['registration_open'] ? 'true' : 'false';


            // Convert BSON arrays to PHP arrays
            foreach (['registered_users', 'participants', 'jury', 'coordinators', 'winners'] as $field) {
                if (isset($contest[$field]) && $contest[$field] instanceof MongoDB\Model\BSONArray) {
                    $contest[$field] = $contest[$field]->getArrayCopy();
                }
            }

            // Convert BSON ObjectIds to strings for rounds

            if (isset($contest['rounds']) && $contest['rounds'] instanceof MongoDB\Model\BSONArray) {
                $contest['rounds'] = array_map(fn($round) => (string) $round, $contest['rounds']->getArrayCopy());
            }
            
        }

        return $contests;
    }


    public static function createContest($title, $description, $contestType, $startTime, $endTime, $registrationDeadline, $facultyId, $coordinators = [])
    {

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

        // foreach ($rounds as $round) {
        //     $roundId = Contest::contestRound($insertedId, $round['title'], $round['round_number'], $round['start_time'], $round['end_time']);

        //     $roundIds[] = $roundId;
        // }

        return $insertedId;
    }


    public static function contestRound($contestId, $name, $roundNumber, $startTime, $endTime, $questions = [], $participants = [], $qualifiedUsers = [])
    {
        $collection = Database::getConnection()->contest_rounds;

        $round = [
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "name" => $name,
            "round_number" => $roundNumber,
            "start_time" => new MongoDB\BSON\UTCDateTime(strtotime($startTime) * 1000),
            "end_time" => new MongoDB\BSON\UTCDateTime(strtotime($endTime) * 1000),
            "questions" => array_map(function ($id) {
                return new MongoDB\BSON\ObjectId($id);
            }, $questions),
            "participants" => array_map(function ($id) {
                return new MongoDB\BSON\ObjectId($id);
            }, $participants),
            "qualified_users" => array_map(function ($id) {
                return new MongoDB\BSON\ObjectId($id);
            }, $qualifiedUsers)
        ];

        $roundId =  $collection->insertOne($round)->getInsertedId();

        if ($roundId) {
            $contestCollection = Database::getConnection()->contests;
            $result =  $contestCollection->updateOne(['_id' => new MongoDB\BSON\ObjectId($contestId)], ['$addToSet' => ['rounds' => $roundId]]);

            return $result ? $roundId : false;
        }
    }

    public static function getRoundNumber($roundId)
    {
        $db = Database::getConnection();

        $round = $db->contest_rounds->findOne(['_id' => new MongoDB\BSON\ObjectId($roundId)]);

        if (!$round) {
            throw new Exception('Round not found');
        }

        return $round['round_number'];
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


    public function getParticipants()
    {
        return $this->contest['participants'];
    }

    public function setJuries($juries)
    {
        // set juries for contest

        $result = $this->collection->updateOne(['_id' => new MongoDB\BSON\ObjectId($this->contestId)], ['$set' => ['jury' => $juries]]);

        return $result->getModifiedCount();
    }

    public function getJuries()
    {
        // get juries for contest
        return $this->contest['jury'];
    }

    public function setWinners() {}

    public function getWinners()
    {
        return $this->contest['winners'];
    }
}
