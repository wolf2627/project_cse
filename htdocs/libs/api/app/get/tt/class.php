<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['faculty_id'])) {


        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $faculty_id = $this->_request['faculty_id'];

        if (!isset($this->_request['status'])) {
            $status = 'active';
        } else {
            $status = $this->_request['status'];
        }


        $result = Classes::getClasses($faculty_id, $status);

        if (!$result) {
            $this->response(
                $this->json([
                    'success' => false,
                    'message' => 'No classes found.'
                ]),
                404 // Internal Server Error
            );
        }

        $this->response(
            $this->json([
                'success' => true,
                'classes' => $result
            ]),
            200
        );
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
