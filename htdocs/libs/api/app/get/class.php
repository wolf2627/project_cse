<?php

// Returns the class details for a particular class ID

${basename(__FILE__, '.php')} = function () {

    if($this->paramsExists('classId')){
        if(!Session::isAuthenticated()){
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $classId = $this->_request['classId'];

        try {
            $class = new Classes();
            $result = $class->getClassDetails($classId);

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