<?php

include 'libs/load.php' ;

require 'vendor/autoload.php'; // Include Composer's autoloader

// MongoDB connection setup
$db = Database::getConnection(); // Replace with your database name
$marksCollection = $db->marks;
$classCollection = $db->classes;

function updateMarksWithClassId($marksCollection, $classCollection) {
    try {
        // Fetch all documents from the Marks collection
        $marksCursor = $marksCollection->find();

        foreach ($marksCursor as $marksDoc) {
            // Match the Class document using relevant fields
            $classDoc = $classCollection->findOne([
                "faculty_id" => $marksDoc["faculty_id"],
                "batch" => $marksDoc["batch"],
                "subject_code" => $marksDoc["subject_code"],
                "department" => $marksDoc["department"],
                "semester" => $marksDoc["semester"],
                "section" => $marksDoc["section"],
            ]);

            if ($classDoc) {
                // Update Marks document with class_id
                $updateResult = $marksCollection->updateOne(
                    ["_id" => $marksDoc["_id"]],
                    [
                        '$set' => ["class_id" => $classDoc["_id"]],
                        '$unset' => [
                            "faculty_id" => "",
                            "batch" => "",
                            "subject_code" => "",
                            "department" => "",
                            "semester" => "",
                            "section" => ""
                        ]
                    ]
                );

                if ($updateResult->getModifiedCount() > 0) {
                    echo "Updated Marks document with _id: " . $marksDoc["_id"] . "\n";
                } else {
                    echo "No changes made for Marks document with _id: " . $marksDoc["_id"] . "\n";
                }
            } else {
                echo "No matching Class document found for Marks document with _id: " . $marksDoc["_id"] . "\n";
            }
        }

        echo "Marks collection updated successfully.\n";
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage() . "\n";
    }
}

// Run the update function
updateMarksWithClassId($marksCollection, $classCollection);

?>
