<?php

// This api returns whether the attendance is marked or not for a particular session

${basename(__FILE__, '.php')} = function () {


    $params = ['department', 'facultyId', 'date', 'day', 'subjectCode', 'section', 'timeslot', 'batch', 'semester'];

    if ($this->paramsExists($params)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $batch = $this->_request['batch'];
        $timeslot = $this->_request['timeslot'];
        $section = $this->_request['section'];
        $semester = $this->_request['semester'];
        $department = $this->_request['department'];
        $subjectCode = $this->_request['subjectCode'];
        $day = $this->_request['day'];
        $date = $this->_request['date'];
        $facultyId = $this->_request['facultyId'];
        $department = $this->_request['department'];

        try {
            $att = new Attendance();
            $result = $att->isMarked($department, $facultyId, $date, $day, $subjectCode, $section, $timeslot, $batch, $semester);

            if ($result) {
                $this->response($this->json([
                    'success' => true,
                    'message' => 'Attendance marked'
                ]), 200);
            } else {
                $this->response($this->json([
                    'success' => false,
                    'message' => 'Attendance not marked'
                ]), 404);
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (preg_match('/\b(?:not|no)\b/i', $errorMessage)) {
                $this->response($this->json([
                    'success' => false,
                    'message' => $errorMessage
                ]), 404);
            } else {
                $this->response($this->json([
                    'success' => false,
                    'message' => $errorMessage
                ]), 500);
            }
        }
    } else {
        $this->response($this->json(['message' => 'Bad request']), 400);
    }
};
