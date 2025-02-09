<?php

${basename(__FILE__, '.php')} = function () {

    if (!Session::isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
        return;
    }

    try {

        if(isset($this->_request['department'])){
            $department = $this->_request['department'];
        } else {
            $department = null;
        }

        if(isset($this->_request['batch'])){
            $batch = $this->_request['batch'];
        } else {
            $batch = null;
        }

        if(isset($this->_request['section'])){
            $section = $this->_request['section'];
        } else {
            $section = null;
        }

        $tutors = Tutor::getTutors($batch, $section, $department);


        if(!$tutors) {
            $this->response($this->json([
                'success' => false,
                'message' => 'No tutors found'
            ]), 404);
        }

        $this->response($this->json([
            'success' => true,
            'tutors' => $tutors
        ]), 200);
    } catch (Exception $e) {
        $this->response($this->json([
            'success' => false,
            'message' => $e->getMessage()
        ]), 500);
    }

};