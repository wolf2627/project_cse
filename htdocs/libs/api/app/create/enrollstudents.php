<?php

// https://domain/api/app/create/enrollstudents
${basename(__FILE__, '.php')} = function () {
    if ($this->paramsExists(['semester', 'section', 'batch', 'year', 'students'])) {
        
        if(!Session::isAuthenticated()){
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }
        error_log('Enrolling students');
        $semester = $this->_request['semester'];
        $section = $this->_request['section'];
        $batch = $this->_request['batch'];
        $year = $this->_request['year'];
        $students = $this->_request['students'];

        $enroll_result = Admin::enrollStudent($students, $semester, $batch, $section, $year);
        if ($enroll_result) {
            $result = [
                'message' => 'Users created successfully',
                'successCount' =>  $enroll_result['success'],
                'failureCount' =>  $enroll_result['failure']
            ];
            $this->response($this->json($result), 200);
        }
    } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
