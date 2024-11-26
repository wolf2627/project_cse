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
                    'Class ID' => $class_id,
                    'Appeared Students' => ($studentMarks === 'nil') ? 0 : $appearedCount,
                    'Pass Count' => ($studentMarks === 'nil') ? 0 : $passCount,
                    'Fail Count' => ($studentMarks === 'nil') ? 0 : $failCount,
                    'Student Marks' => $studentMarks, // Student marks or 'nil'
                    'Failed Students' => $failedStudents // Failed students
                ];
            }
        }

        // Return the structured array section-wise
        return $sectionWiseReport;
    }
}
?>
