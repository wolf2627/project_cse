<?php

${basename(__FILE__, '.php')} = function () {

    //  'section', 'daySlotPairs', 'class_room'
    if ($this->paramsExists(['department', 'subject_code', 'batch', 'semester', 'faculty_id', 'class_id', 'section', 'daySlotPairs', 'class_room'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        // Extract request parameters
        $department = $this->_request['department']; //
        $subject_code = $this->_request['subject_code']; //
        $batch = $this->_request['batch']; //
        $semester = $this->_request['semester']; //
        $faculty_id = $this->_request['faculty_id']; //
        $class_id = $this->_request['class_id']; //
        $section = $this->_request['section']; // 
        $daySlotPairs = $this->_request['daySlotPairs']; //
        $class_room = $this->_request['class_room']; //

        $mergedDaySlotPairs = $daySlotPairs;
        
        try {
            $tt = new TimeTable();
            // $department, $subject_code, $batch, $semester, $faculty_id, $class_id, $section, $class_room, $dayslots
            $result = $tt->assignSlot($department, $subject_code, $batch, $semester, $faculty_id, $class_id, $section, $class_room, $mergedDaySlotPairs);
            $this->response($this->json(['success' => true, 'message' => 'Slot assigned successfully']), 200);
        } catch (Exception $e) {
            $statusCode = ($e->getMessage() === 'Slot already assigned') ? 409 : 500;
            error_log("Error in assignSlot API: " . $e->getMessage());
            $this->response($this->json(['success' => false, 'message' => $e->getMessage()]), $statusCode);
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
