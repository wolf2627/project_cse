<?php

${basename(__FILE__, '.php')} = function () {

    if($this->paramsExists(['student_id'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $student_id = $this->_request['student_id'];

        error_log('Student ID: ' . $student_id);

        $tt = new TimeTable();

        try {
            $result = $tt->getStudentTimeTable($student_id);

            if (!$result) {
            $this->response(
                $this->json([
                'success' => false,
                'message' => 'No timetable found.'
                ]),
                404 // Not Found
            );
            } else {
            $this->response(
                $this->json([
                'success' => true,
                'timetable' => $result
                ]),
                200 // OK
            );
            }
        } catch (Exception $e) {
            error_log('Error fetching timetable: ' . $e->getMessage());
            $this->response(
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ]),
            500 // Internal Server Error
            );
        }

    } else {
        // Bad request response Missing Parameters
        $this->response(
            $this->json([
                'success' => false,
                'message' => 'Bad request'
            ]),
            400 // Bad Request
        );
    }

};