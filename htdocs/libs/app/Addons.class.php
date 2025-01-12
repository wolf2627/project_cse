<?php

class Addons {

    public static function getThirukkural(){

        // Path to the transformed JSON file
        $jsonFile = 'required/files/transformed_thirukkural.json';

        // Read the JSON file
        if (!file_exists($jsonFile)) {
            throw new Exception('Thirukkural file not found.');
        }

        $jsonContent = file_get_contents($jsonFile);
        $data = json_decode($jsonContent, true);

        // Ensure the JSON structure is valid
        if (!$data) {
            die('Error: Invalid structure.');
        }

        // Generate a random number based on the range of available entries
        $randomNumber = rand(1, count($data));

        // Fetch the random entry
        $randomKural = $data[$randomNumber];

        // Display the random entry on the web
        return $randomKural;
    }
}