<?php

class ContestSubmissions
{

    private $db;
    private $collection;
    private $contestId;
    private $roundId;

    public function __construct($contestId, $roundId)
    {
        $this->db = Database::getConnection();
        $this->collection = $this->db->submissions;

        $exist = $this->db->registrations->findOne([
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId)
        ]);

        if (!$exist) {
            throw new Exception('Participant not found');
        }

        $this->contestId = $contestId;
        $this->roundId = $roundId;
    }

    public static function submitCodingAnswer($contestId, $roundId, $questionId, $participantId, $submissionType, $code, $language)
    {
        $db = Database::getConnection();

        $contestsCollection = $db->contests;
        $roundsCollection = $db->contest_rounds;
        $questionsCollection = $db->questions;
        $submissionsCollection = $db->submissions;

        // Validate contest existence
        $contest = $contestsCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($contestId)]);
        if (!$contest) {
            throw new Exception('Contest not found.');
        }

        // Validate round existence
        $round = $roundsCollection->findOne([
            "_id" => new MongoDB\BSON\ObjectId($roundId),
            "contest_id" => new MongoDB\BSON\ObjectId($contestId)
        ]);

        if (!$round) {
            throw new Exception('Contest round not found.');
        }

        // Get current time in UTCDateTime format
        $currentTime = new MongoDB\BSON\UTCDateTime();

        // Convert both timestamps to DateTime objects
        $currentDateTime = $currentTime->toDateTime();
        $endDateTime = $round['end_time']->toDateTime();

        error_log("Current Time: " . $currentDateTime->format('Y-m-d H:i:s'));
        error_log("Round End Time: " . $endDateTime->format('Y-m-d H:i:s'));


        // Compare timestamps
        if ($currentDateTime > $endDateTime) {
            $timeDiff = $currentDateTime->getTimestamp() - $endDateTime->getTimestamp();
            throw new Exception('Submission failed. The contest round ended ' . $timeDiff . ' seconds ago.');
        }


        // Validate question existence
        $question = $questionsCollection->findOne([
            "_id" => new MongoDB\BSON\ObjectId($questionId),
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId)
        ]);

        if (!$question) {
            throw new Exception('Question not found.');
        }

        // Check if participant has already submitted the same question recently
        $recentSubmission = $submissionsCollection->findOne([
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId),
            "question_id" => new MongoDB\BSON\ObjectId($questionId),
            "participant_id" => $participantId,
        ], ['sort' => ['submitted_at' => -1]]);

        if ($recentSubmission) {
            $lastSubmissionTime = $recentSubmission['submitted_at']->toDateTime();
            $timeDiff = (new DateTime())->getTimestamp() - $lastSubmissionTime->getTimestamp();

            if ($timeDiff < 30) { // Prevent multiple submissions within 30 seconds
                throw new Exception("You are submitting too quickly. Please wait before resubmitting.");
            }
        }

        // Sanitize code input (basic)
        $code = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');

        // Prepare submission entry
        $submission = [
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId),
            "question_id" => new MongoDB\BSON\ObjectId($questionId),
            "participant_id" => $participantId,
            "type" => $submissionType,
            "submitted_code" => $code,
            "language" => $language,
            "evaluation_status" => "pending",
            "score" => null,
            "execution_results" => [],
            "submitted_at" => $currentTime
        ];

        // Insert submission
        $insertResult = $submissionsCollection->insertOne($submission);

        if ($insertResult->getInsertedId()) {
            return [
                "success" => true,
                "message" => "Submission successful.",
                "submission_id" => (string) $insertResult->getInsertedId()
            ];
        } else {
            throw new Exception("Failed to submit answer.");
        }
    }


    public static function submitMcqAnswer($contestId, $roundId, $questionId, $participantId, $submissionType, $answer)
    {
        $db = Database::getConnection();

        $collection = $db->submissions;

        $submission = [
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId),
            "question_id" => new MongoDB\BSON\ObjectId($questionId),
            "participant_id" => new MongoDB\BSON\ObjectId($participantId),
            "type" => $submissionType,
            "selected_option" => $answer,
            "is_correct" => null, // This should be determined by the system
            "score" => null, // This should be determined by the system
            "submitted_at" => new MongoDB\BSON\UTCDateTime()
        ];

        return $collection->insertOne($submission)->getInsertedId();
    }


    public static function showSubmissions($contestId, $roundId, $participantId = null)
    {
        $db = Database::getConnection();
        $collection = $db->submissions;

        // Build query dynamically
        $query = [
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId)
        ];

        if ($participantId) {
            $query["participant_id"] = new MongoDB\BSON\ObjectId($participantId);
        }

        // Fetch submissions
        $submissions = $collection->find($query);
        $submissionsArray = iterator_to_array($submissions);

        // Format the output
        return array_map(function ($submission) {
            return [
                "_id" => (string) $submission["_id"],
                "contest_id" => (string) $submission["contest_id"],
                "round_id" => (string) $submission["round_id"],
                "question_id" => (string) $submission["question_id"],
                "participant_id" => (string) $submission["participant_id"],
                "type" => $submission["type"],
                "submitted_code" => $submission["submitted_code"],
                "language" => $submission["language"],
                "evaluation_status" => $submission["status"],
                "score" => $submission["score"],
                "execution_results" => $submission["execution_results"],
                "submitted_at" => $submission["submitted_at"]->toDateTime()->format('Y-m-d H:i:s')
            ];
        }, $submissionsArray);
    }

    //calculate total submissions for a participant
    public static function totalSubmissions($contestId, $roundId, $participantId)
    {
        $db = Database::getConnection();
        $collection = $db->submissions;

        $query = [
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId),
            "participant_id" => new MongoDB\BSON\ObjectId($participantId)
        ];

        return $collection->countDocuments($query);
    }

    //show submitted participants
    public static function showSubmittedParticipants($contestId, $roundId)
    {
        $db = Database::getConnection();
        $collection = $db->submissions;

        $query = [
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId)
        ];

        $participants = $collection->distinct("participant_id", $query);

        return array_map(function ($participant) {
            return (string) $participant;
        }, $participants);
    }
}
