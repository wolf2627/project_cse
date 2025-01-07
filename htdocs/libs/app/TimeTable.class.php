<?php

class TimeTable
{

    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function assignSlotOld($department, $subject_code, $batch, $semester, $faculty_id, $class_id, $section, $day, $slot, $class_room)
    {
        $timetable = $this->conn->timetable;

        // Validate input parameters
        if (
            empty($department) || empty($subject_code) || empty($batch) || empty($semester) ||
            empty($faculty_id) || empty($class_id) || empty($section) || empty($day) || empty($slot) || empty($class_room)
        ) {
            throw new Exception('All fields are required');
        }

        // Check if the slot is already assigned
        $slotExists = $timetable->findOne([
            'class_id' => $class_id,
            'day' => $day,
            'time' => $slot
        ]);

        if ($slotExists) {
            throw new Exception("Slot already assigned on $day at $slot for class $class_id");
        }

        // Insert new slot into the timetable
        $result = $timetable->insertOne([
            'department' => $department,
            'subject_code' => $subject_code,
            'batch' => $batch,
            'semester' => $semester,
            'faculty_id' => $faculty_id,
            'class_id' => $class_id,
            'section' => $section,
            'day' => $day,
            'time' => $slot,
            'class_room' => $class_room
        ]);

        // Check for insertion success
        if ($result->getInsertedCount() == 0) {
            throw new Exception('Failed to assign slot');
        }

        return true;
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

        // Group slots by day
        $slots = [];
        foreach ($dayslots as $entry) {
            $day = $entry['day'] ?? null;
            $slot = $entry['slot'] ?? null;

            if (!$day || !$slot) {
                throw new Exception('Invalid day or slot in input.');
            }

            // Check if the slot is already assigned

            //TODO: Needs improvement on checking and handling slot conflicts
            $slotExists = $timetable->findOne([
                'class_id' => $class_id,
                "dayslots.$day" => ['$in' => [$slot]]
            ]);

            if ($slotExists) {
                throw new Exception("Slot already assigned on $day at $slot for class $class_id.");
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
            'class_id' => $class_id,
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


    public function getTimeTable($student_id)
    {
        $studentsCollection = $this->conn->students;
        $enrollmentsCollection = $this->conn->enrollments;
        $timetableCollection = $this->conn->timetable;


        $student = $studentsCollection->findOne([
            'reg_no' => $student_id
        ]);

        if (!$student) {
            throw new Exception('Student not found');
        }

        $enrollments = $enrollmentsCollection->find([
            'student_id' => $student_id
        ]);

        $timetable = [];

        foreach ($enrollments as $enrollment) {
            $class = $this->conn->classes->findOne([
                '_id' => $enrollment['class_id']
            ]);

            $slots = $timetableCollection->find([
                'class_id' => $enrollment['class_id']
            ]);

            $timetable[] = [
                'subject_code' => $class['subject_code'],
                'subject_name' => $class['subject_name'],
                'faculty_name' => $class['faculty_name'],
                'section' => $class['section'],
                'slots' => iterator_to_array($slots)
            ];
        }

        return $timetable;
    }
}

// {
//     "_id": { "$oid": "timetable123" },
//     "class_id": "673aa6bff8cb080248004c22",  // Reference to the Classes collection
//     "day": "Monday",                        // Day of the week
//     "time": "09:00 - 10:00",                // Time slot
//     "subject_code": "HS2121",               // Cached from Classes collection
//     "faculty_name": "John Doe",             // Cached from Classes collection
//     "section": "A"                          // Cached from Classes collection
//   }
