<?php

class Crons
{

    private $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function run()
    {
        // $this->notifyFaculty();
    }

    // Update attendance for a session (mark attendance) for daily attendance
    // Executed daily at 2:26 AM
    public function updateAttendance()
    {
        // 0. Fetch necessary data
        $day = date('l');
        $date = date('Y-m-d');

        //Ensure the date is a working day
        if ($day == 'Sunday') {
            echo "No classes today\n";
            return;
        }

        // 1. Fetch all faculty
        $faculties = Faculty::getAllFaculties();

        // echo "Faculty count: " . count($faculties) . "\n";

        // 2. Loop through each faculty
        foreach ($faculties as &$faculty) {
            // Fetch timetable for the faculty
            try {
                $tt = new TimeTable();
                $timetable = $tt->getFacultyTimeTable($faculty['faculty_id'], $day);
                $faculty['timetable'] = $timetable;

                // echo "Faculty: " . $faculty['faculty_id'] . " Timetable count: " . count($timetable) . "\n";

                // Loop through each timetable entry
                foreach ($timetable as $entry) {
                    // Fetch attendance for the session
                    $att = new Attendance();
                    $attendance = $att->markSession($faculty['faculty_id'], $date, $day, $entry['time'], $entry['class_id']);

                    if ($attendance) {
                        Log::dolog("Attendance marked for faculty: {$faculty['faculty_id']} for session:  {$entry['time']}", 'cron', true);
                    } else {
                        Log::dolog("Attendance not marked for faculty: {$faculty['faculty_id']} for session:  {$entry['time']}", 'cron', true);
                    }
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                Log::dolog("{$faculty['faculty_id']}:  {$e->getMessage()} - {$entry['time']}", 'cron', true);
                continue;
            }
        }
    }
}