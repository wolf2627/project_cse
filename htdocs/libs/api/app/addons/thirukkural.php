<?php

${basename(__FILE__, '.php')} = function () {
    try {
        $randomKural = Addons::getThirukkural();
        $this->response($this->json($randomKural), 200);
    } catch (Exception $e) {
        $this->response($this->json(['message' => $e->getMessage()]), 500);
    }
};
