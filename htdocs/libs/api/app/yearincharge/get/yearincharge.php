<?php

${basename(__FILE__, '.php')} = function () {


    if (!Session::isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
        return;
    }

    $faculty_id = isset($this->_request['faculty_id']) ? $this->_request['faculty_id'] : null;
    $department = isset($this->_request['department']) ? $this->_request['department'] : null;
    $batch = isset($this->_request['batch']) ? $this->_request['batch'] : null;

    try {

        $yearInCharges = YearInCharge::getAssignedYearIncharges($faculty_id, $department, $batch);

        if (!$yearInCharges) {
            $this->response($this->json([
                'success' => false,
                'message' => 'No year in charge found'
            ]), 404);
        }

        $this->response($this->json([
            'success' => true,
            'yearincharges' => $yearInCharges
        ]), 200);
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
};
