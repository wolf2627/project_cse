<?php

// This api handles both marking the attendance and editing the attendance

${basename(__FILE__, '.php')} = function () {

    error_log("Save Edit API");

    $params = ['sessionId', 'classId', 'attendanceData'];

    if ($this->paramsExists($params)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $sessionId = $this->_request['sessionId'];
        $classId = $this->_request['classId'];
        $attendanceData = $this->_request['attendanceData'];
        $edit = true;

        // Converting the attendance data to an array
        // $attendanceData = json_decode($attendanceData, true);

        $att = new Attendance();

        $SessionDetails = $att->getSessionDetails($sessionId);

        $facultyId = $SessionDetails['faculty_id'];
        $date = $SessionDetails['date'];
        $day = $SessionDetails['day'];
        $timeslot = $SessionDetails['timeslot'];

        $class = new Classes();

        $classDetails = $class->getClassDetails($classId);

        $department = $classDetails['department'];
        $subjectCode = $classDetails['subject_code'];
        $section = $classDetails['section'];
        $batch = $classDetails['batch'];
        $semester = $classDetails['semester'];


        error_log("Department: $department" . " Faculty ID: $facultyId" . " Date: $date" . " Day: $day" . " Subject Code: $subjectCode" . " Section: $section" . " Timeslot: $timeslot" . " Batch: $batch" . " Semester: $semester" . " Attendance Data: $attendanceData");

        // Converting the attendance data to an array
        $attendanceData = json_decode($attendanceData, true);

        $att = new Attendance();

        try {


            $result = $att->saveAttendance($department, $facultyId, $date, $day, $subjectCode, $section, $timeslot, $batch, $semester, $attendanceData, $edit);

            if ($result) {
                $this->response($this->json([
                    'success' => true,
                    'message' => 'Attendance marked successfully'
                ]), 200);
            } else {
                $this->response($this->json([
                    'success' => false,
                    'message' => 'Attendance not marked'
                ]), 500);
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
