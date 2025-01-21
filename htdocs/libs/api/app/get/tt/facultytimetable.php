<?php

${basename(__FILE__, '.php')} = function () {

    if($this->paramsExists(['faculty_id'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $faculty_id = $this->_request['faculty_id'];

        error_log('Faculty ID: ' . $faculty_id);

        $tt = new TimeTable();

        try {
            $result = $tt->getFacultyTimeTable($faculty_id);

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
                'message' => 'An error occurred while fetching the timetable.'
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