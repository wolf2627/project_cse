<?php

// This api handles both marking the attendance and editing the attendance

${basename(__FILE__, '.php')} = function () {

    $params = ['department', 'facultyId', 'date', 'day', 'subjectCode', 'section', 'timeslot', 'batch', 'semester', 'attendanceData'];

    if ($this->paramsExists($params)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $department = $this->_request['department'];
        $facultyId = $this->_request['facultyId'];
        $date = $this->_request['date'];
        $day = $this->_request['day'];
        $subjectCode = $this->_request['subjectCode'];
        $section = $this->_request['section'];
        $timeslot = $this->_request['timeslot'];
        $batch = $this->_request['batch'];
        $semester = $this->_request['semester'];
        $attendanceData = $this->_request['attendanceData'];

        if (isset($this->_request['edit'])) {
            $edit = $this->_request['edit'];
            error_log("Edit: $edit");
        } else {
            $edit = false;
            error_log("Edit: $edit");
        }


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
