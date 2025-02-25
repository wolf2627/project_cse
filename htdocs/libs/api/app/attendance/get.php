<?php


// This API returns the mark attendance form for a particular session

${basename(__FILE__, '.php')} = function () {

    if($this->paramsExists('sessionId')){
        if(!Session::isAuthenticated()){
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $sessionId = $this->_request['sessionId'];

        try {
            $att = new Attendance();
            $result = $att->getAttendance($sessionId);

            $this->response($this->json([
                'success' => true,
                'message' => $result
            ]), 200);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (preg_match('/\b(?:not|no)\b/i', $errorMessage)) {
                $this->response($this->json([
                    'success' => false,
                    'message' => $errorMessage]), 404);
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