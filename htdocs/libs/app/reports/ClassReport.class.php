<?php

class ClassReport
{
    private $conn = null;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function getSectionWiseReport($test_id)
    {
        $testDetails = Test::getTestDetails($test_id);

        if (!$testDetails) {
            return "Test not found!";
        }

        $subjectCollection = $this->conn->subjects;
        $classesCollection = $this->conn->classes;
        $marksCollection = $this->conn->marks; // Marks collection
        $facultiesCollection = $this->conn->faculties;

        $passmarks = (int)$testDetails->passmarks; // Convert passmarks to integer

        $sectionWiseReport = [];

        // Loop through subjects in the test
        foreach ($testDetails->subjects as $subject) {
            $subject_code = $subject->subject_code;
            $batch = $testDetails->batch;
            $semester = $testDetails->semester;

            // Get subject name from the subjects collection
            $subjectDetails = $subjectCollection->findOne(array('subject_code' => $subject_code));
            $subject_name = $subjectDetails ? $subjectDetails->subject_name : 'Unknown Subject';

            // Fetch all classes for the subject, batch, and semester
            $classes = $classesCollection->find(array(
                'subject_code' => $subject_code,
                'batch' => $batch,
                'semester' => $semester
            ));

            // Loop through each class for the subject
            foreach ($classes as $class) {
                $class_id = (string) $class['_id']; // Convert class_id to string
                $section = $class['section'];
                $faculty_id = (string) $class['faculty_id'];
                $faculty_name = Faculty::getFacultyName($faculty_id);

                // Initialize counters for each section
                $appearedCount = 0;
                $passCount = 0;
                $failCount = 0;

                // Initialize an array to store the student marks for this section
                $studentMarks = [];

                $failedStudents = [];

                // Fetch marks for the current class_id and test_id
                $marksData = $marksCollection->find(array(
                    'class_id' => new MongoDB\BSON\ObjectId($class_id),
                    'test_id' => new MongoDB\BSON\ObjectId($test_id)
                ))->toArray();

                if (count($marksData) > 0) {
                    // Process marks data
                    foreach ($marksData as $marksEntry) {
                        foreach ($marksEntry['marks'] as $studentMark) {
                            if ($studentMark['reg_no']) {
                                // Count as appeared
                                $appearedCount++;

                                // Check if student passed or failed
                                if ($studentMark['marks'] >= $passmarks) {
                                    $passCount++;
                                } else {
                                    $failCount++;
                                    $failedStudents[] = [
                                        'Reg No' => $studentMark['reg_no'],
                                        'Student Name' => $studentMark['studentname'],
                                        'Marks' => $studentMark['marks']
                                    ];
                                }

                                // Add student marks to the array
                                $studentMarks[] = [
                                    'Reg No' => $studentMark['reg_no'],
                                    'Student Name' => $studentMark['studentname'],
                                    'Marks' => $studentMark['marks']
                                ];
                            }
                        }
                    }
                } else {
                    // No marks available, set as 'nil'
                    $studentMarks = 'nil';
                }

                // Store results by section (A, B, C, D) for each subject
                if (!isset($sectionWiseReport[$section])) {
                    $sectionWiseReport[$section] = [
                        'Section' => $section,
                        'Subjects' => []
                    ];
                }

                $sectionWiseReport[$section]['Subjects'][$subject_code] = [
                    'Subject Name' => $subject_name,
                    'Subject Code' => $subject_code,
                    'Faculty Name' => $faculty_name,
                    'Class ID' => $class_id,
                    'Appeared Students' => ($studentMarks === 'nil') ? 0 : $appearedCount,
                    'Pass Count' => ($studentMarks === 'nil') ? 0 : $passCount,
                    'Pass Percentage' => ($studentMarks === 'nil' || $appearedCount === 0) ? 0 : round(($passCount / $appearedCount) * 100, 2),
                    'Fail Count' => ($studentMarks === 'nil') ? 0 : $failCount,
                    'Student Marks' => $studentMarks, // Student marks or 'nil'
                    'Failed Students' => $failedStudents // Failed students
                ];
            }
        }

        // Return the structured array section-wise
        return $sectionWiseReport;
    }


    public function SeparateFailedStudentsBySubjects($data)
    {

        if (!$data) {
            throw new Exception("No data provided");
        }

        $failed_students_by_subject = array();
        foreach ($data as $section_key => $section_data) {
            // Loop through each subject in the section
            foreach ($section_data["Subjects"] as $subject_code => $subject_data) {
                // If subject doesn't exist in the combined array, create an entry for it
                if (!isset($failed_students_by_subject[$subject_code])) {
                    $failed_students_by_subject[$subject_code] = array(
                        "Subject Name" => $subject_data["Subject Name"],
                        "Failed Students" => array(),
                        "Total Failed Count" => 0 // Initialize total count
                    );
                }

                // Add the section information for each student and merge failed students
                foreach ($subject_data["Failed Students"] as $failed_student) {
                    $failed_student["Section"] = $section_data["Section"]; // Add section info to each student
                    $failed_students_by_subject[$subject_code]["Failed Students"][] = $failed_student;
                }

                // Update the total count of failed students for this subject
                $failed_students_by_subject[$subject_code]["Total Failed Count"] = count($failed_students_by_subject[$subject_code]["Failed Students"]);
            }
        }

        return $failed_students_by_subject;
    }


    public function calculatefailedsubjects($data)
    {
        if (!$data) {
            throw new Exception("No data provided");
        }

        // Loop through each section
        // Initialize an array to track the number of failed subjects for each student
        $failed_subjects_per_student = array();

        // Loop through each section
        foreach ($data as $section_key => $section_data) {
            // Loop through each subject in the section
            foreach ($section_data["Subjects"] as $subject_code => $subject_data) {
                // Add failed students from this subject
                foreach ($subject_data["Failed Students"] as $failed_student) {
                    // Add section info to student
                    $failed_student["Section"] = $section_data["Section"];
                    $failed_student["Subject Name"] = $subject_data["Subject Name"];

                    // If student hasn't been tracked yet, initialize their record
                    if (!isset($failed_subjects_per_student[$failed_student["Reg No"]])) {
                        $failed_subjects_per_student[$failed_student["Reg No"]] = array(
                            "Student Name" => $failed_student["Student Name"],
                            "Failed Subjects" => array()
                        );
                    }

                    // Add the subject and section to the student's failed subjects array
                    $failed_subjects_per_student[$failed_student["Reg No"]]["Failed Subjects"][] = array(
                        "Section" => $failed_student["Section"],
                        "Subject Name" => $failed_student["Subject Name"]
                    );
                }
            }
        }

        // Now categorize students based on the number of failed subjects
        $failure_categories = array();

        // Loop through the failed students and categorize them
        foreach ($failed_subjects_per_student as $student_reg_no => $student_data) {
            $failed_subjects_count = count($student_data["Failed Subjects"]);

            // If the category doesn't exist, initialize it
            if (!isset($failure_categories[$failed_subjects_count])) {
                $failure_categories[$failed_subjects_count] = array(
                    'students' => array(),
                    'count' => 0
                );
            }

            // Add the student to the corresponding failure category
            $failure_categories[$failed_subjects_count]['students'][] = array(
                "Reg No" => $student_reg_no,
                "Student Name" => $student_data["Student Name"],
                'Section' => $student_data["Failed Subjects"][0]["Section"],
                "Failed Subjects" => $student_data["Failed Subjects"]
            );

            // Increase the count for this category
            $failure_categories[$failed_subjects_count]['count']++;
        }

        ksort($failure_categories);

        return $failure_categories;
    }
}
