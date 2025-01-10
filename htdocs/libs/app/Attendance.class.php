<?php

class Attendance
{

    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function saveAttendance($department, $facultyId, $date, $day ,$subjectCode, $section, $timeslot, $batch, $semester, $attendanceData, $edit = false)
    {

        error_log("Edit from func: $edit");
        $attSession = $this->conn->attendance_session;

        // Step 1: Validate Input
        if (empty($facultyId) || empty($date) || empty($subjectCode) || empty($section) || empty($timeslot) || empty($batch) || empty($semester) || !is_array($attendanceData)) {
            throw new InvalidArgumentException("Invalid input parameters.");
        }

        // Step 2: Check or Create Attendance Session

        //step 2.1: Get class id
        $class_id = Classes::getClassId($batch, $semester, $subjectCode, $section, $department, $facultyId);

        if(!$class_id) {
            throw new Exception("Class not found.");
        }

        $class_id = new MongoDB\BSON\ObjectId($class_id);

        //step 2.2: Check if attendance session already exists
        $attendanceSession = $attSession->findOne([
            'faculty_id' => $facultyId,
            'date' => $date,
            'day' => $day,
            // 'subject_code' => $subjectCode,
            // 'section' => $section,
            'timeslot' => $timeslot,
            // 'batch' => $batch,
            // 'semester' => $semester,
            'class_id' => $class_id
        ]);

        if(!$edit && $attendanceSession && $attendanceSession['students_marked']) {
            throw new Exception("Attendance already marked for the session.");
        }

        if (!$attendanceSession) {
            $attendanceSession = [
                'faculty_id' => $facultyId,
                'date' => $date,
                'day' => $day,
                // 'subject_code' => $subjectCode,
                // 'section' => $section,
                'timeslot' => $timeslot,
                // 'batch' => $batch,
                // 'semester' => $semester,
                'class_id' => $class_id,
                'marked_at' => new MongoDB\BSON\UTCDateTime(new DateTime()),
                'students_marked' => false,
            ];

            $result = $attSession->insertOne($attendanceSession);
            if (empty($result->getInsertedCount())) {
                throw new Exception("Failed to initialize attendance session.");
            }
            $attendanceSession['_id'] = $result->getInsertedId();
        }

        $sessionId = $attendanceSession['_id'] ?? null;
        if (!$sessionId) {
            throw new Exception("Failed to retrieve attendance session ID.");
        }

        // Step 3: Prepare Bulk Operations for Attendance Records
        $bulkOps = [];
        foreach ($attendanceData as $student) {
            if (!isset($student['id'], $student['status'])) {
                error_log("Invalid student data: " . json_encode($student));
                continue;
            }

            $bulkOps[] = [
                'updateOne' => [
                    ['attendance_session_id' => $sessionId, 'student_id' => $student['id']],
                    ['$set' => [
                        'attendance_session_id' => $sessionId,
                        'student_id' => $student['id'],
                        'status' => $student['status'],
                        'marked_at' => new MongoDB\BSON\UTCDateTime(new DateTime()),
                    ]],
                    ['upsert' => true],
                ],
            ];
        }

        if (empty($bulkOps)) {
            throw new Exception("No valid attendance data to process.");
        }

        // Step 4: Execute Bulk Operations
        try {
            $attendanceCollection = $this->conn->attendance;
            $result = $attendanceCollection->bulkWrite($bulkOps);

            // Mark attendance session as completed
            $updateResult = $attSession->updateOne(
                ['_id' => $sessionId],
                ['$set' => ['students_marked' => true]]
            );

            if (empty($updateResult->getModifiedCount())) {
                // throw new Exception("Failed to update attendance session.");
                error_log("Failed to update attendance session.");
            }
        } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
            foreach ($e->getWriteResult()->getWriteErrors() as $error) {
                error_log("Bulk Write Error: " . $error->getMessage());
            }
            throw new Exception("Bulk write failed: " . $e->getMessage());
        }

        return [
            'session_id' => $sessionId,
            'processed_count' => count($bulkOps),
        ];
    }


    public function isMarked($department ,$facultyId, $date, $day, $subjectCode, $section, $timeslot, $batch, $semester)
    {
        $attSession = $this->conn->attendance_session;

        $class_id = Classes::getClassId($batch, $semester, $subjectCode, $section, $department, $facultyId);
        ;

        if(!$class_id) {
            throw new Exception("Class not found.");
        }

        $class_id = new MongoDB\BSON\ObjectId($class_id);

        $attendanceSession = $attSession->findOne([
            'faculty_id' => $facultyId,
            'date' => $date,
            'day' => $day,
            'timeslot' => $timeslot,
            'class_id' => $class_id
        ]);

        return $attendanceSession ? $attendanceSession['students_marked'] : false;
    }


    public function getMarkedSessions($facultyId)
    {
        $attSession = $this->conn->attendance_session;

        $sessions = $attSession->find([
            'faculty_id' => $facultyId,
            'students_marked' => true
        ]);

        $markedSessions = [];
        foreach ($sessions as $session) {
            $markedSessions[] = [
            '_id' => (string)$session['_id'],
            'faculty_id' => (string)$session['faculty_id'],
            'date' => (string)$session['date'],
            'day' => (string)$session['day'],
            'timeslot' => (string)$session['timeslot'],
            'class_id' => (string)$session['class_id'],
            'marked_at' => (string)$session['marked_at'],
            'students_marked' => (string)$session['students_marked'],
            ];
        }

        return $markedSessions;
    }

    // To be Tested
    public function getAttendance($session_id)
    {
        $attendance = $this->conn->attendance;

        $records = $attendance->find([
            'attendance_session_id' => new MongoDB\BSON\ObjectId($session_id)
        ]);

        $attendanceData = [];

        foreach ($records as $record) {
            $attendanceData[] = [
                '_id' => (string)$record['_id'],
                'student_id' => (string)$record['student_id'],
                'attendance_session_id' => (string)$record['attendance_session_id'],
                'marked_at' => (string)$record['marked_at'],
                'status' => (string)$record['status'],
            ];
        }

        return $attendanceData;
    }

    // Needs to updated as per the new schema
    public function getStudentAttendance($studentId)
    {

        $individual_attendance = $this->conn->attendance;

        $records = $individual_attendance->aggregate([
            ['$match' => ['student_id' => $studentId]],
            [
                '$group' => [
                    '_id' => '$subject_code',
                    'total_classes' => ['$sum' => 1],
                    'attended' => ['$sum' => ['$cond' => [['$eq' => ['$status', 'Present']], 1, 0]]]
                ]
            ],
            [
                '$project' => [
                    'subject_code' => '$_id',
                    'total_classes' => 1,
                    'attended' => 1,
                    'percentage' => ['$multiply' => [['$divide' => ['$attended', '$total_classes']], 100]]
                ]
            ]
        ]);

        $attendance = [];
        foreach ($records as $record) {
            $attendance[] = $record;
        }

        if (empty($attendance)) {
            throw new Exception("No attendance records found for the student.");
        }

        return $attendance;
    }

    // Needs to updated as per the new schema
    public function getPendingAttendance($facultyId)
    {
        $attendanceSession = $this->conn->attendance_session;

        $pendingSessions = $attendanceSession->find([
            'faculty_id' => $facultyId,
            'students_marked' => false,
            'date' => ['$lte' => new MongoDB\BSON\UTCDateTime()]
        ]);

        $pending = [];
        foreach ($pendingSessions as $session) {
            $pending[] = $session;
        }

        if(empty($pending)) {
            throw new Exception("No pending attendance sessions found.");
        }

        return $pending;
    }

    // Needs to updated as per the new schema
    public function getOverallAttendancePercentage($studentId)
    {

        $individual_attendance = $this->conn->attendance;

        $records = $individual_attendance->aggregate([
            ['$match' => ['student_id' => $studentId]],
            [
                '$group' => [
                    '_id' => null,
                    'total_classes' => ['$sum' => 1],
                    'attended' => ['$sum' => ['$cond' => [['$eq' => ['$status', 'Present']], 1, 0]]]
                ]
            ],
            [
                '$project' => [
                    'overall_percentage' => ['$multiply' => [['$divide' => ['$attended', '$total_classes']], 100]]
                ]
            ]
        ]);

        $result = iterator_to_array($records);
        return !empty($result) ? $result[0]['overall_percentage'] : 0;
    }
}
