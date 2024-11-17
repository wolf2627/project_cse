<?php

// https://domain/api/app/create/createtest

${basename(__FILE__, '.php')} = function () {
    // Required parameters
    $requiredParams = ['testname', 'month', 'batch', 'semester', 'year', 'department', 'subjects', 'duration', 'totalmarks', 'passmarks', 'instructions'];
    try {
        // Check if required parameters exist
        if ($this->paramsExists($requiredParams)) {

            // Ensure the user is authenticated
            if (!Session::isAuthenticated()) {
                $this->response($this->json(['message' => 'Unauthorized']), 401);
           }

            error_log('Creating...');

            // Extract parameters from request
            $testname = $this->_request['testname'];
            $month = $this->_request['month'];
            $batch = $this->_request['batch'];
            $semester = $this->_request['semester'];
            $year = $this->_request['year'];
            $department = $this->_request['department'];
            $subjects = $this->_request['subjects'];
            $duration = $this->_request['duration'];
            $totalmarks = $this->_request['totalmarks'];
            $passmarks = $this->_request['passmarks'];
            $instructions = $this->_request['instructions'];

            // Validate parameters if needed
            // if (!is_array($subjects) || empty($subjects)) {
            //     $this->response($this->json(['message' => 'Invalid subjects parameter']), 400);
            //     return;
            // }

            // if (!is_numeric($totalmarks) || !is_numeric($passmarks)) {
            //     $this->response($this->json(['message' => 'Total Marks and Pass Marks should be numeric']), 400);
            //     return;
            // }

            // Call the core logic to create the test
            $result = Admin::createTest($testname, $month, $batch, $semester, $year, $department, $subjects, $duration, $totalmarks, $passmarks, $instructions);
            error_log('Result: ' . $result);

            // $result = 'duplicate';
            // Prepare and send the response
            if ($result === true) {
                $this->response($this->json(['message' => 'Success']), 200);
            } else if ($result === 'duplicate') {
                $this->response($this->json(['message' => 'Test Already Exists']), 409);
            } else {
                $this->response($this->json(['message' => 'Assignment failed']), 500);
            }
        } else {
            // Missing parameters
            $this->response($this->json(['message' => 'Bad request: Missing required parameters']), 400);
        }
    } catch (Exception $e) {
        throw new Exception($e);
        //$this->response($this->json(['message' => $e]), 500);
    }
};
