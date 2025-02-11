<?php

class ContestQuestions
{

    public static function addQuestionsToRound($contestId, $roundId, $questionId)
    {

        $collection = Database::getConnection()->contest_rounds;
       
        try {
            $collection->updateOne(
                ["contest_id" => new MongoDB\BSON\ObjectId($contestId), "_id" => new MongoDB\BSON\ObjectId($roundId)],
                [
                    '$addToSet' => ["questions" => new MongoDB\BSON\ObjectId($questionId)]
                ]
            );
        } catch (Exception $e) {
            throw new Exception('Question already exists in round');
        }
    }

    public static function removeQuestionsFromRound($contestId, $roundNumber, $questionIds)
    {
        $db = Database::getConnection();

        $result = $db->contests->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($contestId), "rounds.round_number" => $roundNumber],
            [
                '$pull' => ["rounds.$.questions" => ['$in' => array_map(fn($id) => new MongoDB\BSON\ObjectId($id), $questionIds)]]
            ]
        );

        return $result->getModifiedCount() > 0;
    }

    public static function getQuestionsForRound($contestId, $roundNumber)
    {
        $db = Database::getConnection();
        $contest = $db->contests->findOne(["_id" => new MongoDB\BSON\ObjectId($contestId)]);

        if (!$contest) {
            throw new Exception('Contest not found');
        }

        $round = array_filter($contest['rounds'], fn($round) => $round['round_number'] == $roundNumber);

        if (empty($round)) {
            throw new Exception('Round not found');
        }

        return $round[0]['questions'];
    }

    public static function getQuestionDetails($questionId)
    {
        $db = Database::getConnection();
        $question = $db->questions->findOne(["_id" => new MongoDB\BSON\ObjectId($questionId)]);

        if (!$question) {
            throw new Exception('Question not found');
        }

        return $question;
    }

    public static function addMCQQuestion($type, $title, $options, $correct_option_index, $contestId, $roundId)
    {
        if ($type != "quiz") {
            throw new Exception('Invalid question type');
        }

        $db = Database::getConnection();
        $collection = $db->questions;

        $exists = $collection->findOne(["type" => $type, "title" => $title]);

        if ($exists) {
            throw new Exception('Question already exists');
        }

        $question = [
            "type" => $type,
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId),
            "title" => $title,
            "options" => $options,
            "correct_option_index" => $correct_option_index,
            "created_at" => new MongoDB\BSON\UTCDateTime(),
            "updated_at" => new MongoDB\BSON\UTCDateTime()
        ];

        return $collection->insertOne($question)->getInsertedId();
    }


    public static function addCodingQuestion($type, $title, $description, $input_format, $output_format, $test_cases, $difficulty, $contestId, $roundId)
    {
        $db = Database::getConnection();
        $collection = $db->questions;

        if ($type != "coding") {
            throw new Exception('Invalid question type');
        }

        $exists = $collection->findOne(["type" => $type, "title" => $title]);

        if ($exists) {
            throw new Exception('Question already exists');
        }

        $question = [
            "type" => $type,
            "contest_id" => new MongoDB\BSON\ObjectId($contestId),
            "round_id" => new MongoDB\BSON\ObjectId($roundId),
            "title" => $title,
            "description" => $description,
            "input_format" => $input_format,
            "output_format" => $output_format,
            "test_cases" => $test_cases,
            "difficulty" => $difficulty,
            "created_at" => new MongoDB\BSON\UTCDateTime(),
            "updated_at" => new MongoDB\BSON\UTCDateTime()
        ];

        try {
            $questionId =  $collection->insertOne($question)->getInsertedId();
        } catch (Exception $e) {
            throw new Exception('Question already exists');
        }

        // Add question to round
        ContestQuestions::addQuestionsToRound($contestId, $roundId, $questionId);

        return $questionId;
    }
}

// Sample document in questions collection:

// {
//     "_id": ObjectId(),
//     "contest_id": ObjectId(),
//     "round_id": ObjectId(),
//     "type": "coding",  // ["coding", "quiz"]
//     "title": "Find the factorial",
//     "description": "Write a program to find the factorial of a number.",
//     "input_format": "An integer n",
//     "output_format": "An integer (n!)",
//     "test_cases": [
//       {
//         "input": "5",
//         "expected_output": "120"
//       },
//       {
//         "input": "7",
//         "expected_output": "5040"
//       }
//     ],
//     "difficulty": "medium"  // ["easy", "medium", "hard"]
//   }
  
// {
//     "_id": ObjectId(),
//     "contest_id": ObjectId(),
//     "round_id": ObjectId(),
//     "type": "quiz",
//     "title": "Capital of France",
//     "options": ["Paris", "London", "Rome", "Berlin"],
//     "correct_option_index": 0
//   }
  