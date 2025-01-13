<?php

class Attendance
{

    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function saveAttendance($department, $facultyId, $date, $day, $subjectCode, $section, $timeslot, $batch, $semester, $attendanceData, $edit = false)
    {

        error_log("Edit from func: $edit");
        $attSession = $this->conn->attendance_session;

        // Step 1: Validate Input
        if (empty($facultyId) || empty($date) || empty($subjectCode) || empty($section) || empty($timeslot) || empty($batch) || empty($semester) || !is_array($attendanceData)) {
            throw new InvalidArgumentException("Invalid input parameters.");
        }

        // Step 2: Check or Create Attendance Session

        error_log("Department: $department" . " Faculty ID: $facultyId" . " Date: $date" . " Day: $day" . " Subject Code: $subjectCode" . " Section: $section" . " Timeslot: $timeslot" . " Batch: $batch" . " Semester: $semester");
        //step 2.1: Get class id
        $class_id = Classes::getClassId($batch, $semester, $subjectCode, $section, $department, $facultyId);

        if (!$class_id) {
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

        if (!$edit && $attendanceSession && $attendanceSession['students_marked']) {
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
                'status' => 'on-going'
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
                ['$set' => ['marked_at' => new MongoDB\BSON\UTCDateTime(new DateTime()), 'students_marked' => true, 'status' => $edit ? 'modified' : 'marked']]
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


    public function isMarked($department, $facultyId, $date, $day, $subjectCode, $section, $timeslot, $batch, $semester)
    {
        $attSession = $this->conn->attendance_session;

        $class_id = Classes::getClassId($batch, $semester, $subjectCode, $section, $department, $facultyId);;

        if (!$class_id) {
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

    /**
     * Get pending attendance sessions for a faculty.
     * @param string $facultyId
     */
    public function getPendingAttendance($facultyId)
    {

        if (!Faculty::verify($facultyId)) {
            throw new Exception("Faculty not found.");
        }

        $attendanceSession = $this->conn->attendance_session;

        $pendingSessions = $attendanceSession->find([
            'faculty_id' => $facultyId,
            'students_marked' => false,
            //'date' => ['$lte' => new MongoDB\BSON\UTCDateTime()]
        ]);

        $pending = [];
        foreach ($pendingSessions as $session) {
            $pending[] = [
                '_id' => (string)$session['_id'],
                'faculty_id' => (string)$session['faculty_id'],
                'date' => (string)$session['date'],
                'day' => (string)$session['day'],
                'timeslot' => (string)$session['timeslot'],
                'class_id' => (string)$session['class_id'],
            ];
        }

        if (empty($pending)) {
            throw new Exception("No pending attendance found.");
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


    public function getMarkedFacultyAttendance($facultyId, $class_id, $date){
        $attendanceSession = $this->conn->attendance_session;


        if(!Faculty::verify($facultyId)){
            throw new Exception("Faculty not found.");
        }

        if(!Classes::verify($class_id)){
            throw new Exception("Class not found.");
        }

        if(empty($date)){
            throw new Exception("Invalid date.");
        }


        $markedSessions = $attendanceSession->find([
            'faculty_id' => $facultyId,
            'class_id' => new MongoDB\BSON\ObjectId($class_id),
            'date' => $date,
            'students_marked' => true
        ]);

        if(empty($markedSessions)){
            throw new Exception("No marked attendance found.");
        }

        $marked = [];
        foreach ($markedSessions as $session) {
            $marked[] = [
                '_id' => (string)$session['_id'],
                'faculty_id' => (string)$session['faculty_id'],
                'date' => (string)$session['date'],
                'day' => (string)$session['day'],
                'timeslot' => (string)$session['timeslot'],
                'class_id' => (string)$session['class_id'],
            ];
        }


        $att = $this->conn->attendance;

        $attendanceData = [];

        foreach ($marked as $session) {
            $records = $att->find([
                'attendance_session_id' => new MongoDB\BSON\ObjectId($session['_id'])
            ]);

            $attendance = [];
            foreach ($records as $record) {
                $attendance[] = [
                    '_id' => (string)$record['_id'],
                    'student_id' => (string)$record['student_id'],
                    'attendance_session_id' => (string)$record['attendance_session_id'],
                    'marked_at' => (string)$record['marked_at'],
                    'status' => (string)$record['status'],
                ];
            }

            usort($attendance, function ($a, $b) {
                return strcmp($a['student_id'], $b['student_id']);
            });

            foreach($attendance as $key => $value){
                $student = new Student($value['student_id']);
                $studentDetails = $student->getStudentDetails($value['student_id']);
                $attendance[$key]['student_name'] = $studentDetails['name'];
            }

            $attendanceData[] = [
                'session' => $session,
                'attendance' => $attendance
            ];
        }

        if(empty($attendanceData)){
            throw new Exception("No attendance records found.");
        }

        return $attendanceData;
    }


    public function getfacultyMarkedClasses($facultyId){
        $attendanceSession = $this->conn->attendance_session;

        $markedSessions = $attendanceSession->find([
            'faculty_id' => $facultyId,
            'students_marked' => true
        ]);

        if(empty($markedSessions)){
            throw new Exception("No marked attendance found.");
        }

        $marked = [];
        foreach ($markedSessions as $session) {
            $marked[] = [
                '_id' => (string)$session['_id'],
                'faculty_id' => (string)$session['faculty_id'],
                'date' => (string)$session['date'],
                'day' => (string)$session['day'],
                'timeslot' => (string)$session['timeslot'],
                'class_id' => (string)$session['class_id'],
            ];
        }

        return $marked;
    }


    public function getSessionDetails($session_id){
        
        $sess = $this->conn->attendance_session;

        $session = $sess->findOne([
            '_id' => new MongoDB\BSON\ObjectId($session_id)
        ]);

        if(!$session){
            throw new Exception("Session not found.");
        }

        $result = Essentials::convertArray($session);

        return $result;

    }

}
