<?php

class ContestParticipants
{

    private $db;
    private $participantId;

    public function __construct($participantId, $contestId)
    {
        $this->db = Database::getConnection();

        $collection = $this->db->registrations;

        $participant = $collection->findOne([
            "participant_id" => new MongoDB\BSON\ObjectId($participantId),
            "contest_id" => new MongoDB\BSON\ObjectId($contestId)
        ]);


        if (!$participant) {
            throw new Exception('Participant not found');
        }

        $this->participantId = $participantId;
    }

    public function startContest($contestId, $roundId)
    {
        $collection = $this->db->participants;

        $pariticipated = $collection->findOne([
            "participant_id" => $this->participantId,
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId)
        ]);

        if ($pariticipated['status'] == 'started') {
            throw new Exception('Participant already started the contest');
        }

        if ($pariticipated['status'] == 'ended') {
            throw new Exception('Participant already ended the contest');
        }

        $participant = [
            "participant_id" => $this->participantId,
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId),
            "started_at" => new MongoDB\BSON\UTCDateTime(),
            "ended_at" => null,
            "status" => "started"
        ];

        $contestStarted = $collection->insertOne($participant)->getInsertedId() ? true : false;

        if ($contestStarted) {
            return ContestQuestions::getQuestionsForRound($contestId, Contest::getRoundNumber($roundId));
        } else {
            throw new Exception('Failed to start contest');
        }
    }

    public function endContest()
    {

        $collection = $this->db->participants;

        $pariticipated = $collection->findOne([
            "participant_id" => $this->participantId,
            "status" => "started"
        ]);

        if (!$pariticipated) {
            throw new Exception('Participant not started the contest');
        }

        $result = $collection->updateOne(
            [
                "participant_id" => $this->participantId,
                "status" => "started"
            ],
            [
                '$set' => [
                    "status" => "ended",
                    "ended_at" => new MongoDB\BSON\UTCDateTime()
                ]
            ]
        );

        return $result->getModifiedCount() > 0;
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
