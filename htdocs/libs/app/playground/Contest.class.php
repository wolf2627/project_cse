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

        $contest = $this->collection->findOne(['_id' => new MongoDB\BSON\ObjectId($contestId)]);

        if (!$contest) {
            throw new Exception('Contest not found');
        }

        // Convert BSONDocument to PHP Array
        $contestArray = Essentials::bsonToArray($contest);

        // echo "<pre>";
        // print_r($contestArray); // Debugging
        // echo "</pre>";
        $this->contestId = $contestId;
        $this->contest = $contestArray;
    }

    public function getTitle()
    {
        return $this->contest['title'];
    }

    public function getDescription()
    {
        return $this->contest['description'];
    }

    public function getStartTime($timezone = 'Asia/Kolkata')
    {
        $startTime = new DateTime(
            $this->contest['start_time'],
            new DateTimeZone('UTC')
        );
        $startTime->setTimezone(new DateTimeZone($timezone)); // Convert to IST
        return $startTime->format('d-m-Y H:i:s'); // Output: 16-06-2021 05:30:00
    }
   

    public function getEndTime($timezone = 'Asia/Kolkata')
    {
        $endTime = new DateTime(
            $this->contest['end_time'],
            new DateTimeZone('UTC')
        );
        $endTime->setTimezone(new DateTimeZone($timezone)); // Convert to IST
        return $endTime->format('d-m-Y H:i:s'); // Output: 16-06-2021 05:30:00
    }

    public function getRegistrationDeadline($timezone = 'Asia/Kolkata')
    {
        $registrationDeadline = new DateTime(
            $this->contest['registration_deadline'],
            new DateTimeZone('UTC')
        );
        $registrationDeadline->setTimezone(new DateTimeZone($timezone)); // Convert to IST
        return $registrationDeadline->format('d-m-Y H:i:s'); // Output: 16-06-2021 05:30:00
    }


    public function getStatus(){
        return $this->contest['status'];
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

    public function setCoordinators($coordinators)
    {
        // Ensure coordinators is an array
        if (!is_array($coordinators)) {
            throw new InvalidArgumentException('Coordinators must be an array');
        }

        // set coordinators for contest
        $result = $this->collection->updateOne(['_id' => new MongoDB\BSON\ObjectId($this->contestId)], ['$set' => ['coordinators' => $coordinators]]);
        return $result->getModifiedCount();
    }

    public function adddCoordinator($coordinator)
    {
        // append a coordinator for contest
        $result = $this->collection->updateOne(['_id' => new MongoDB\BSON\ObjectId($this->contestId)], ['$addToSet' => ['coordinators' => $coordinator]]);
        return $result->getModifiedCount();
    }

    public function removeCoordinator($coordinator)
    {
        // remove a coordinator for contest
        $result = $this->collection->updateOne(['_id' => new MongoDB\BSON\ObjectId($this->contestId)], ['$pull' => ['coordinators' => $coordinator]]);
        return $result->getModifiedCount();
    }

    public function getCoordinators()
    {
        return $this->contest['coordinators'];
    }

    public function isCoordinator($userId)
    {
        //print_r($this->contest['coordinators']);
        return in_array($userId, $this->contest['coordinators']);
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
}
