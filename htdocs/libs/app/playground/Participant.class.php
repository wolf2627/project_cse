<?php

class ContestParticipants
{

    private $db;
    private $participantId;
    private $contestId;

    public function __construct($participantId, $contestId)
    {
        $this->db = Database::getConnection();

        $collection = $this->db->registrations;

        $participant = $collection->findOne([
            "student_id" => $participantId,
            "contest_id" => new MongoDB\BSON\ObjectId($contestId)
        ]);


        if (!$participant) {
            throw new Exception('Participant not found');
        }

        $this->participantId = $participantId;
        $this->contestId = $contestId;
    }

    public function startContest($roundId)
    {
        $collection = $this->db->participants;
        $roundsCollection = $this->db->contest_rounds;
    
        // Ensure the round exists
        $round = $roundsCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($roundId)]);
        if (!$round) {
            throw new Exception("Round not found.");
        }
    
        $currentTime = new MongoDB\BSON\UTCDateTime();
        $roundEndTime = $round['end_time'];
    
        // Check if the round has already ended
        if ($currentTime > $roundEndTime) {
            throw new Exception("This round has already ended. You cannot start it now.");
        }
    
        // Check if the participant has already started another contest
        $ongoingParticipation = $collection->findOne([
            "participant_id" => $this->participantId,
            "status" => "started"
        ]);
    
        // Handle unfinished attempts if the round has already ended
        if ($ongoingParticipation) {
            if ($ongoingParticipation['started_at'] < $roundEndTime) {
                throw new Exception("You are already participating in another contest.");
            } else {
                // Mark previous attempt as expired
                $collection->updateOne(
                    ["_id" => $ongoingParticipation["_id"]],
                    ['$set' => ["status" => "expired", "ended_at" => $roundEndTime]]
                );
            }
        }
    
        // Insert participant data
        $participantData = [
            "participant_id" => $this->participantId,
            "contest_id" => new MongoDB\BSON\ObjectId($this->contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId),
            "started_at" => $currentTime,
            "ended_at" => null,
            "status" => "started"
        ];
    
        $insertResult = $collection->insertOne($participantData);
        if (!$insertResult->getInsertedId()) {
            throw new Exception("Failed to start contest.");
        }
    
        // Return questions for the round
        return ContestQuestions::getQuestionsForRound($this->contestId, Contest::getRoundNumber($roundId));
    }
    

    public function endContest()
    {
        $collection = $this->db->participants;
        $roundsCollection = $this->db->contest_rounds;
    
        // Fetch participant data
        $participation = $collection->findOne([
            "participant_id" => $this->participantId,
            "status" => "started"
        ]);
    
        if (!$participation) {
            throw new Exception('Participant has not started the contest.');
        }
    
        // Get round details
        $round = $roundsCollection->findOne([
            "_id" => new MongoDB\BSON\ObjectId($participation["round_id"])
        ]);
    
        if (!$round) {
            throw new Exception('Contest round not found.');
        }
    
        $currentTime = new MongoDB\BSON\UTCDateTime();
        $roundEndTime = $round['end_time'];
    
        // Check if the round has already ended
        if ($currentTime > $roundEndTime) {
            // Mark as expired if participant didn't complete before round ended
            $status = "expired";
        } else {
            // Normal contest completion
            $status = "ended";
        }
    
        // Ensure we don’t re-end a contest
        if (!empty($participation['ended_at'])) {
            throw new Exception("Contest already ended at " . $participation['ended_at']->toDateTime()->format('Y-m-d H:i:s'));
        }
    
        // Update participant status
        $result = $collection->updateOne(
            [
                "participant_id" => $this->participantId,
                "status" => "started"
            ],
            [
                '$set' => [
                    "status" => $status,
                    "ended_at" => $currentTime
                ]
            ]
        );
    
        if ($result->getModifiedCount() > 0) {
            return true;
        } else {
            throw new Exception("Failed to update contest status.");
        }
    }
    


    public static function addParticipantToContest($contestId, $participantId)
    {
        $collection = Database::getConnection()->contests;

        try {
            $collection->updateOne(
                ["_id" => new MongoDB\BSON\ObjectId($contestId)],
                [
                    '$addToSet' => ["participants" => new MongoDB\BSON\ObjectId($participantId)]
                ]
            );
        } catch (Exception $e) {
            throw new Exception('Participant already exists in contest');
        }
    }

    public static function removeParticipantFromContest($contestId, $participantId)
    {
        $db = Database::getConnection();

        $result = $db->contests->updateOne(
            [
                "_id" => new MongoDB\BSON\ObjectId($contestId)
            ],
            [
                '$pull' => [
                    "participants" => new MongoDB\BSON\ObjectId($participantId)
                ]
            ]
        );

        return $result->getModifiedCount() > 0;
    }

    public static function getParticipantsForContest($contestId)
    {
        $db = Database::getConnection();
        $contest = $db->contests->findOne(["_id" => new MongoDB\BSON\ObjectId($contestId)]);

        if (!$contest) {
            throw new Exception('Contest not found');
        }

        return $contest['participants'];
    }
}
