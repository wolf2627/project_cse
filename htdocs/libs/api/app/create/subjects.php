<?php

// https://domain/api/users/createuser
${basename(__FILE__, '.php')} = function () {
    if (isset($_FILES['subjects_file'])) {
        if(!Session::isAuthenticated()){
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }
        $file = $_FILES['subjects_file']['tmp_name'];
        $app = new Admin();
        $creation_result = $app->createSubjects($file);
        
        if ($creation_result) {
            $result = [
                'message' => 'Subjects created successfully',
                'successCount' => $creation_result['success'],
                'failureCount' => $creation_result['failure']
            ];
            $this->response($this->json($result), 200);
        }
    } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
