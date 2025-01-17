<?php

class Attendance
{

    private $conn;

    /**
     * Constructor for the Attendance class.
     */
    public function __construct()
    {
        $this->conn = Database::getConnection();
    }


    public function markSession($facultyId, $date, $day, $timeslot, $class_id)
    {
        $attSession = $this->conn->attendance_session;

        // Step 1: Validate Input
        if (empty($facultyId) || empty($date) || empty($day) || empty($timeslot) || empty($class_id)) {
            throw new InvalidArgumentException("Invalid input parameters.");
        }

        $class = Classes::verify($class_id);

        if (!$class) {
            throw new Exception("Class not found.");
        }

        $class_id = new MongoDB\BSON\ObjectId($class_id);

        //step 2.2: Check if attendance session already exists

        $attendanceSession = $attSession->findOne([
            'faculty_id' => $facultyId,
            'date' => $date,
            'day' => $day,
            'timeslot' => $timeslot,
            'class_id' => $class_id
        ]);

        if ($attendanceSession) {
            throw new Exception("Attendance already marked for the session.");
        }


        $attendanceSession = [
            'faculty_id' => $facultyId,
            'date' => $date,
            'day' => $day,
            'timeslot' => $timeslot,
            'class_id' => $class_id,
            'marked_at' => new MongoDB\BSON\UTCDateTime(new DateTime()),
            'students_marked' => false,
            'status' => 'not marked'
        ];

        $result = $attSession->insertOne($attendanceSession);

        if (empty($result->getInsertedCount())) {
            throw new Exception("Failed to initialize attendance session.");
        }

        $attendanceSession['_id'] = $result->getInsertedId();

        return $attendanceSession;
    }




    /**
     * Mark attendance for a session. 
     * edit flag is used to determine if the attendance is being marked for the first time or being edited.
     * if edit flag is set to true, the attendance session is marked as modified. which means the attendance session is already marked and is being edited.
     * 
     * @param string $department
     * @param string $facultyId
     * @param string $date
     * @param string $day
     * @param string $subjectCode
     * @param string $section
     * @param string $timeslot
     * @param string $batch
     * @param string $semester
     * @param array $attendanceData
     * @param bool $edit
     * 
     * @return array
     */
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

    /**
     * Check if attendance is marked for a session.
     * @param string $department
     * @param string $facultyId
     * @param string $date
     * @param string $day
     * @param string $subjectCode
     * @param string $section
     * @param string $timeslot
     * @param string $batch
     * @param string $semester
     * 
     * @return bool
     */

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

    /**
     * Get marked attendance sessions for a faculty.
     * @param string $facultyId
     * 
     * @return array
     */
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

    /**
     * Get attendance records for a session.
     * @param string $session_id
     * 
     * @return array
     */
    public function getAttendance($session_id)
    {
        $attendance = $this->conn->attendance;

        $records = $attendance->find([
            'attendance_session_id' => new MongoDB\BSON\ObjectId($session_id)
        ]);

        if (empty($records)) {
            throw new Exception("No attendance records found.");
        }

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


    /**
     * Get pending attendance sessions for a faculty.
     * @param string $facultyId
     * 
     * @return array
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


    /**
     * Get marked attendance for a faculty.
     * @param string $facultyId
     * @param string $class_id
     * @param string $date
     * 
     * @return array
     */

    public function getMarkedFacultyAttendance($facultyId, $class_id, $date)
    {
        $attendanceSession = $this->conn->attendance_session;


        if (!Faculty::verify($facultyId)) {
            throw new Exception("Faculty not found.");
        }

        if (!Classes::verify($class_id)) {
            throw new Exception("Class not found.");
        }

        if (empty($date)) {
            throw new Exception("Invalid date.");
        }


        $markedSessions = $attendanceSession->find([
            'faculty_id' => $facultyId,
            'class_id' => new MongoDB\BSON\ObjectId($class_id),
            'date' => $date,
            'students_marked' => true
        ]);

        if (empty($markedSessions)) {
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

            foreach ($attendance as $key => $value) {
                $student = new Student($value['student_id']);
                $studentDetails = $student->getStudentDetails($value['student_id']);
                $attendance[$key]['student_name'] = $studentDetails['name'];
            }

            $attendanceData[] = [
                'session' => $session,
                'attendance' => $attendance
            ];
        }

        if (empty($attendanceData)) {
            throw new Exception("No attendance records found.");
        }

        return $attendanceData;
    }

    /**
     * Get marked classes for a faculty.
     * @param string $facultyId
     * 
     * @return array
     */

    public function getfacultyMarkedClasses($facultyId)
    {
        $attendanceSession = $this->conn->attendance_session;

        $markedSessions = $attendanceSession->find([
            'faculty_id' => $facultyId,
            'students_marked' => true
        ]);

        if (empty($markedSessions)) {
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


    /**
     * Get attendance records for a session.
     * @param string $session_id
     * 
     * @return array
     */
    public function getSessionDetails($session_id)
    {

        $sess = $this->conn->attendance_session;

        $session = $sess->findOne([
            '_id' => new MongoDB\BSON\ObjectId($session_id)
        ]);

        if (!$session) {
            throw new Exception("Session not found.");
        }

        $result = Essentials::convertArray($session);

        return $result;
    }

    public function getStudentAttendance($studentId)
    {

        if (!Student::verify($studentId)) {
            throw new Exception("Student not found.");
        }
    }


    public function calculateAttendanceSubjectWise($studentId)
    {
        $attendanceCollection = $this->conn->attendance;
        $attendanceSessionCollection = $this->conn->attendance_session;

        // Get enrolled classes for the student
        $stud = new Student($studentId);
        $enrolledClasses = $stud->getEnrolledClasses();

        if (empty($enrolledClasses)) {
            throw new Exception("No classes found.");
        }

        // Prepare subject-wise attendance structure
        $subjectAttendance = [];
        foreach ($enrolledClasses as $class) {
            $subjectAttendance[$class['subject_code']] = [
                'attended' => 0,
                'on_duty' => 0,
                'total' => 0,
                'sessions' => [] // To store date-wise session details
            ];
        }

        // Process attendance sessions for enrolled classes
        foreach ($enrolledClasses as $class) {
            $classId = $class['class_id'];
            $subjectCode = $class['subject_code'];

            // Find all attendance sessions for this class
            $attendanceSessions = $attendanceSessionCollection->find([
                'class_id' => new MongoDB\BSON\ObjectId($classId)
            ]);

            foreach ($attendanceSessions as $session) {
                $sessionId = (string) $session['_id'];
                $sessionDate = $session['date'];
                $timeSlot = $session['timeslot']; // e.g., '09:40-10:35'

                // Handle multiple sessions for the same class on the same date
                if (!isset($subjectAttendance[$subjectCode]['sessions'][$sessionDate])) {
                    $subjectAttendance[$subjectCode]['sessions'][$sessionDate] = [];
                }
                $subjectAttendance[$subjectCode]['sessions'][$sessionDate][$timeSlot] = [
                    'status' => 'Absent',
                    'session_id' => $sessionId,
                    'marked_at' => isset($session['marked_at']) ? $session['marked_at']->toDateTime()->setTimezone(new DateTimeZone('Asia/Kolkata'))->format('d-m-Y H:i:s') : null,
                ];

                $subjectAttendance[$subjectCode]['total']++;

                // Check if the student attended this session
                $attendanceRecord = $attendanceCollection->findOne([
                    'attendance_session_id' => new MongoDB\BSON\ObjectId($sessionId),
                    'student_id' => $studentId
                ]);

                if ($attendanceRecord) {
                    $status = $attendanceRecord['status'];
                    if ($status === 'present') {
                        $subjectAttendance[$subjectCode]['attended']++;
                        $subjectAttendance[$subjectCode]['sessions'][$sessionDate][$timeSlot]['status'] = 'Present';
                    } elseif ($status === 'on-duty') {
                        $subjectAttendance[$subjectCode]['on_duty']++;
                        $subjectAttendance[$subjectCode]['sessions'][$sessionDate][$timeSlot]['status'] = 'On-Duty';
                    }
                }
            }
        }

        // Calculate overall attendance percentage (including on-duty)
        $totalAttended = 0;
        $totalOnDuty = 0;
        $totalSessions = 0;

        foreach ($subjectAttendance as $subjectData) {
            $totalAttended += $subjectData['attended'];
            $totalOnDuty += $subjectData['on_duty'];
            $totalSessions += $subjectData['total'];
        }

        $overallAttendancePercentage = $totalSessions > 0
            ? (($totalAttended + $totalOnDuty) / $totalSessions) * 100
            : 0;

        // Format subject-wise attendance percentages
        foreach ($subjectAttendance as &$subjectData) {
            $subjectData['percentage'] = $subjectData['total'] > 0
                ? (($subjectData['attended'] + $subjectData['on_duty']) / $subjectData['total']) * 100
                : 0;
        }

        return [
            'overallAttendancePercentage' => round($overallAttendancePercentage, 2),
            'subjectAttendance' => $subjectAttendance
        ];
    }

    public function calculateAttendanceByDate($studentId, $date = null)
    {
        $attendanceCollection = $this->conn->attendance;
        $attendanceSessionCollection = $this->conn->attendance_session;

        $timeTableSlots = Essentials::loadtimeTableSlots();

        // Get enrolled classes for the student
        $cls = new Student($studentId);
        $enrolledClasses = $cls->getEnrolledClasses();

        if (empty($enrolledClasses)) {
            throw new Exception("No classes found.");
        }

        // Create a map of class IDs to subject codes
        $classToSubjectMap = [];
        foreach ($enrolledClasses as $class) {
            $classToSubjectMap[(string)$class['class_id']] = $class['subject_code'];
        }

        // Build the query for attendance sessions
        $sessionQuery = [
            'class_id' => ['$in' => array_map(function ($class) {
                return new MongoDB\BSON\ObjectId($class['class_id']);
            }, $enrolledClasses)]
        ];
        if ($date) {
            $sessionQuery['date'] = $date; // Filter by date if provided
        }

        // Fetch all attendance sessions for the enrolled classes
        $attendanceSessions = $attendanceSessionCollection->find($sessionQuery);

        // Prepare date-wise attendance structure
        $dateWiseAttendance = [];

        foreach ($attendanceSessions as $session) {
            $sessionId = (string)$session['_id'];
            $sessionDate = $session['date']; // Assuming 'date' is in 'Y-m-d' format
            $timeSlot = $session['timeslot']; // e.g., '08:45-09:40'
            $classId = (string)$session['class_id'];
            $subjectCode = $classToSubjectMap[$classId];

            // Initialize date if not already present
            if (!isset($dateWiseAttendance[$sessionDate])) {
                $dateWiseAttendance[$sessionDate] = [
                    'sessions' => [],
                    'summary' => [
                        'total_present' => 0,
                        'total_absent' => 0,
                        'total_on_duty' => 0,
                        'total_not_marked' => 0
                    ]
                ];
            }

            // Initialize session if not already present
            if (!isset($dateWiseAttendance[$sessionDate]['sessions'][$timeSlot])) {
                $dateWiseAttendance[$sessionDate]['sessions'][$timeSlot] = [
                    'time_slot' => $timeSlot,
                    'subject_code' => $subjectCode, // Subject Code for the class
                    'status' => 'Not Marked', // Default to "Not Marked"
                    'marked_at' => isset($session['marked_at']) ? $session['marked_at']->toDateTime()->format('Y-m-d H:i:s') : null
                ];
            }

            // Fetch the attendance record for the student in this session
            $attendanceRecord = $attendanceCollection->findOne([
                'attendance_session_id' => new MongoDB\BSON\ObjectId($sessionId),
                'student_id' => $studentId
            ]);

            if ($attendanceRecord) {
                $status = $attendanceRecord['status'];
                if ($status === 'present') {
                    $dateWiseAttendance[$sessionDate]['sessions'][$timeSlot]['status'] = 'Present';
                    $dateWiseAttendance[$sessionDate]['summary']['total_present']++;
                } elseif ($status === 'on-duty') {
                    $dateWiseAttendance[$sessionDate]['sessions'][$timeSlot]['status'] = 'On-Duty';
                    $dateWiseAttendance[$sessionDate]['summary']['total_on_duty']++;
                } elseif ($status === 'absent') {
                    $dateWiseAttendance[$sessionDate]['sessions'][$timeSlot]['status'] = 'Absent';
                    $dateWiseAttendance[$sessionDate]['summary']['total_absent']++;
                }
            } else {
                $dateWiseAttendance[$sessionDate]['summary']['total_not_marked']++;
            }
        }

        // Fill missing sessions for each date using the timetable slots
        foreach ($dateWiseAttendance as $dateKey => &$attendanceData) {
            foreach ($timeTableSlots as $slotKey => $slotTime) {
                // Skip breaks and lunch (if desired)
                if ($slotKey === 'Break' || $slotKey === 'Lunch') {
                    continue;
                }

                // Check if this slot exists, if not, add it as "Not Marked"
                if (!isset($attendanceData['sessions'][$slotTime])) {
                    $attendanceData['sessions'][$slotTime] = [
                        'time_slot' => $slotTime,
                        'subject_code' => null,
                        'status' => 'Not Marked',
                        'marked_at' => null
                    ];
                    $attendanceData['summary']['total_not_marked']++;
                }
            }
        }

        return $date ? $dateWiseAttendance[$date] ?? [] : $dateWiseAttendance;
    }
}
