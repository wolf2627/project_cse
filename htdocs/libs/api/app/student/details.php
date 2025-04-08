<?php

${basename(__FILE__, '.php')} = function () {


    $requiredParams = ['student_id'];

    if ($this->paramsExists($requiredParams)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $student_id =  $this->_request['student_id'];

        $Student = new Student($student_id);

        $studentDetails = $Student->getStudentDetails();

        $enrolledClasses = $Student->getEnrolledClasses();

        $this->response($this->json([
            'success' => true,
            'studentDetails' => $studentDetails,
            'enrolledClasses' => $enrolledClasses
        ]), 200);

    } else {

        $this->response($this->json(['message' => 'Bad request']), 400);
    }

};