<?php

class TimeTable
{

    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function assignSlot($department, $subject_code, $batch, $semester, $faculty_id, $class_id, $section, $class_room, $dayslots)
    {
        $timetable = $this->conn->timetable;

        // Validate input parameters
        if (
            empty($department) || empty($subject_code) || empty($batch) || empty($semester) ||
            empty($faculty_id) || empty($class_id) || empty($section) || empty($class_room) || empty($dayslots)
        ) {
            throw new Exception('All fields are required.');
        }

        // Decode dayslots if it is a JSON string
        if (is_string($dayslots)) {
            $dayslots = json_decode($dayslots, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format for dayslots.');
            }
        }

        // Check for faculty existence
        if (!$this->conn->faculties->findOne(['faculty_id' => $faculty_id])) {
            throw new Exception('Faculty not found.');
        }

        // Check for class existence
        if (!$this->conn->classes->findOne(['_id' => new MongoDB\BSON\ObjectId($class_id)])) {
            throw new Exception('Class not found.');
        }

        // Check if the faculty is already assigned to the class
        $facultyAssigned = $timetable->findOne([
            'faculty_id' => $faculty_id,
            'class_id' => new MongoDB\BSON\ObjectId($class_id)
        ]);

        if ($facultyAssigned) {
            throw new Exception('Faculty already assigned to the class. Remove or update the existing assignment.');
        }

        // Group slots by day
        $slots = [];
        foreach ($dayslots as $entry) {
            $day = $entry['day'] ?? null;
            $slot = $entry['slot'] ?? null;

            if (!$day || !$slot) {
                throw new Exception('Invalid day or slot in input.');
            }

            // Check if the slot is already assigned
            $slotExists = $timetable->findOne([
                'class_id' => new MongoDB\BSON\ObjectId($class_id),
                "dayslots.$day" => ['$in' => [$slot]]
            ]);

            if ($slotExists) {
                throw new Exception("Slot already assigned on $day at $slot for class $class_id.");
            }

            // Check for faculty availability
            $facultyExists = $timetable->findOne([
                'faculty_id' => $faculty_id,
                "dayslots.$day" => ['$in' => [$slot]]
            ]);

            if ($facultyExists) {
                throw new Exception("Faculty already assigned on $day at $slot.");
            }

            // Add the slot to the appropriate day
            $slots[$day][] = $slot;
        }

        // Insert the document
        $result = $timetable->insertOne([
            'department' => $department,
            'subject_code' => $subject_code,
            'batch' => $batch,
            'semester' => $semester,
            'faculty_id' => $faculty_id,
            'class_id' => new MongoDB\BSON\ObjectId($class_id),
            'section' => $section,
            'class_room' => $class_room,
            'dayslots' => $slots // Save grouped slots as an array of arrays
        ]);

        // Ensure insertion was successful
        if (!$result->getInsertedCount()) {
            throw new Exception('Failed to assign slot.');
        }

        return true;
    }


    public function getFacultyTimeTable($faculty_id, $find_day = 'all')
    {

        // echo "Faculty ID: $faculty_id <br>";
        // echo "Day: $find_day <br>";
        $timetable = $this->conn->timetable;

        // Validate input parameters
        if (empty($faculty_id)) {
            throw new Exception('Faculty ID is required.');
        }

        // Check for faculty existence
        if (!$this->conn->faculties->findOne(['faculty_id' => $faculty_id])) {
            throw new Exception('Faculty not found.');
        }

        // Find timetable entries for the faculty
        $cursor = $timetable->find(['faculty_id' => $faculty_id]);

        // Initialize an empty array to store the day-wise timetable
        $dayWiseTimetable = [];

        // Iterate over the cursor
        foreach ($cursor as $entry) {
            foreach ($entry['dayslots'] as $day => $slots) {
                foreach ($slots as $timeSlot) {
                    $dayWiseTimetable[$day][] = [
                        'class_id' => (string) $entry['class_id'],
                        'class' => $entry['class_room'],
                        'semester' => $entry['semester'],
                        'batch' => $entry['batch'],
                        'section' => $entry['section'],
                        'subject_code' => $entry['subject_code'],
                        'department' => $entry['department'],
                        'time' => $timeSlot,
                    ];
                }
            }
        }

        // If no timetable entries are found for the faculty, throw an exception
        if (empty($dayWiseTimetable)) {
            throw new Exception('No timetable found for the faculty.');
        }


        // if day is specified, filter the timetable for the day
        if ($find_day !== 'all') {
            // echo "Day: $find_day <br>";
            $dayWiseTimetable = $dayWiseTimetable[$find_day] ?? [];
        }


        return $dayWiseTimetable;
    }

    public function getStudentTimeTable($student_id)
    {
        // Initialize the Student object
        $student = new Student($student_id);
    
        // Get the enrolled classes for the student
        $enrolled_classes = $student->getEnrolledClasses();

       // print_r($enrolled_classes);
    
        // Initialize an empty array to store the overall timetable
        $dayWiseTimetable = [];
    
        // Iterate through each enrolled class
        foreach ($enrolled_classes as $class) {
            $timetable = $this->conn->timetable;
    
            // Ensure class_id is converted to ObjectId
            $class_id = new MongoDB\BSON\ObjectId($class['class_id']);

            // print_r($class_id);
    
            // Fetch timetable entries for the current class
            $cursor = $timetable->find(['class_id' => $class_id]);
    
            // Convert cursor to array for processing
            $entries = iterator_to_array($cursor);
    
            // If no timetable entries are found for the class, skip it
            if (empty($entries)) {

                echo "<br>No timetable entries found for class $class_id <br>";

                continue;
            }
    

            // Process each timetable entry
            foreach ($entries as $entry) {
    
                foreach ($entry['dayslots'] as $day => $slots) {
                    foreach ($slots as $timeSlot) {
                        $dayWiseTimetable[$day][] = [
                            'class' => $entry['class_room'],
                            'semester' => $entry['semester'],
                            'batch' => $entry['batch'],
                            'section' => $entry['section'],
                            'subject_code' => $entry['subject_code'],
                            'department' => $entry['department'],
                            'time' => $timeSlot,
                        ];
                    }
                }

               //  echo $class_id;


            }
        }

        // If no timetable entries are found for any class, throw an exception
        if (empty($dayWiseTimetable)) {
            throw new Exception('No timetable found for the student.');
        }
    
        return $dayWiseTimetable;
    }
    
}