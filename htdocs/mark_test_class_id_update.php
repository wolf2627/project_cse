<?php

include 'libs/load.php';

require 'vendor/autoload.php'; // Include Composer's autoloader

// MongoDB connection setup
$db = Database::getConnection();
$marksCollection = $db->marks;
$classCollection = $db->classes;

function replaceMarksCollection($marksCollection, $classCollection) {
    try {
        // Fetch all documents from the Marks collection
        $marksCursor = $marksCollection->find();

        // Array to hold new marks data
        $newMarksData = [];

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
                // Construct new marks document with class_id and test_id
                $newMarksData[] = [
                    "_id" => $marksDoc["_id"], // Keep original _id
                    "test_id" => $marksDoc["test_id"], // Assuming test_id is already present
                    "class_id" => $classDoc["_id"], // Referencing class_id
                    "marks" => $marksDoc["marks"], // Keeping marks array as-is
                    "Entered_at" => (new DateTime())->format('Y-m-d H:i:s'),
                ];
            } else {
                echo "No matching Class document found for Marks document with _id: " . $marksDoc["_id"] . "\n";
            }
        }

        // Clear the Marks collection
        $deleteResult = $marksCollection->deleteMany([]);
        echo "Deleted " . $deleteResult->getDeletedCount() . " documents from Marks collection.\n";

        // Insert the new marks data
        if (!empty($newMarksData)) {
            $insertResult = $marksCollection->insertMany($newMarksData);
            echo "Inserted " . $insertResult->getInsertedCount() . " new documents into Marks collection.\n";
        } else {
            echo "No new data to insert into Marks collection.\n";
        }

        echo "Marks collection replaced successfully.\n";
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage() . "\n";
    }
}

// Run the replace function
replaceMarksCollection($marksCollection, $classCollection);

?>
