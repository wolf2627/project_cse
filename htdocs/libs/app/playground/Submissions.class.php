<?php

class ContestSubmissions
{
    public static function submitCodingAnswer($contestId, $roundId, $questionId, $participantId, $submissionType, $code = null, $language = null)
    {
        $db = Database::getConnection();

        $collection = $db->submissions;

        $submission = [
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId),
            "question_id" => new MongoDB\BSON\ObjectId($questionId),
            "participant_id" => new MongoDB\BSON\ObjectId($participantId),
            "type" => $submissionType,
            "submitted_code" => $code,
            "language" => $language,
            "status" => "pending",
            "score" => null,
            "execution_results" => [],
            "submitted_at" => new MongoDB\BSON\UTCDateTime()
        ];

        return $collection->insertOne($submission)->getInsertedId();
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


}
