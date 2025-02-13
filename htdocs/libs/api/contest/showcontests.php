<?php

${basename(__FILE__, '.php')} = function () {


    if (!$this->isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
        return;
    }

    if (isset($_GET['status'])) {
        $status = $_GET['status'];

        if ($status == 'upcoming' || $status == 'running' || $status == 'completed') {
            $contests = Contest::showContests($status);
            $this->response($this->json([
                'message' => 'success',
                'contests' => $contests
            ]), 200);
        } else {
            $this->response($this->json(['message' => 'Invalid status']), 400);
        }
    } else {
        $status = 'upcoming';

        $contests = Contest::showContests($status);

        $this->response($this->json([
            'message' => 'success',
            'contests' => $contests
        ]), 200);
    }
};
