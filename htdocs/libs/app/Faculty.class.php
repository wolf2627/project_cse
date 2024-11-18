<?php

class Faculty
{

    private $faculty_id;
    private $conn;

    public function __construct()
    {
        $this->faculty_id = Session::getUser()->getFacultyId();
        $this->conn = Database::getConnection();
    }

    public function getFacultyId()
    {
        return $this->faculty_id;
    }

    /**
     * Fetch classes handled by the faculty.
     */
    public function getClasses()
    {
        $collection = $this->conn->classes;

        try {
            $cursor = $collection->find(
                ['faculty_id' => $this->faculty_id],
                ['projection' => ['_id' => 0]]
            );

            return $cursor->toArray();
        } catch (Exception $e) {
            error_log('Error fetching classes: ' . $e->getMessage());
            return false;
        }
    }

    public function getClass($subject_code)
    {
        $collection = $this->conn->classes;

        try {
            $cursor = $collection->findOne(
                ['faculty_id' => $this->faculty_id, 'subject_code' => $subject_code],
                ['projection' => ['_id' => 0]]
            );

            $result = iterator_to_array($cursor);

            return $result ?: false;
        } catch (Exception $e) {
            error_log('Error fetching classes: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch subjects assigned to the faculty.
     */
    public function getSubjects()
    {
        $collection = $this->conn->classes;

        try {
            $cursor = $collection->find(
                ['faculty_id' => $this->faculty_id],
                ['projection' => ['subject_code' => 1, '_id' => 0]]
            );

            $result = $cursor->toArray();
            $subjectCodes = array_map(fn($item) => $item['subject_code'], $result);

            return $subjectCodes ?: false;
        } catch (Exception $e) {
            error_log('Error fetching subjects: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch tests assigned to the faculty's subjects.
     */
    public function getFacultyAssignedTests()
    {
        $testCollection = $this->conn->tests;

        try {
            $subjectCodes = $this->getSubjects();
            if (isset($subjectCodes['error'])) {
                throw new Exception($subjectCodes['error']);
            }

            $cursor = $testCollection->find([
                'status' => 'active',
                'subjects.subject_code' => ['$in' => $subjectCodes]
            ]);

            $result = [];
            foreach ($cursor as $test) {
                $testName = $test['testname'];

                // Convert BSONArray to a PHP array
                $subjectsArray = (array) $test['subjects'];

                // Filter and map the subjects
                $testSubjects = array_column(array_filter(
                    $subjectsArray,
                    fn($subject) => in_array($subject['subject_code'], $subjectCodes)
                ), 'subject_code');

                // Merge the subjects into the result
                $result[$testName] = array_merge($result[$testName] ?? [], $testSubjects);
            }

            return $result ?: throw new Exception('No tests found.');
        } catch (Exception $e) {
            error_log('Error fetching tests: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Insert marks for a test if not already present.
     */
    public function enterMark($batch, $semester, $subject_code, $testname, $section, $marks)
    {
        $collection = $this->conn->marks;

        // echo "Enter Marks : ". $batch . " " . $semester . " " . $subject_code . " " . $testname . " " . $section . " " . $marks;

        try {
            $existing = $collection->findOne([
                'faculty_id' => $this->faculty_id,
                'batch' => $batch,
                'semester' => $semester,
                'subject_code' => $subject_code,
                'test_name' => $testname,
                'section' => $section
            ]);

            if ($existing) {
                return 'duplicate';
            }

            $data = [
                'faculty_id' => $this->faculty_id,
                'batch' => $batch,
                'semester' => $semester,
                'subject_code' => $subject_code,
                'test_name' => $testname,
                'section' => $section,
                'marks' => $marks,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $collection->insertOne($data);
            return true;
        } catch (Exception $e) {
            error_log('error => Error entering marks:' . $e->getMessage());
            return false;
        }
    }

    public function getMarks($batch, $semester, $subject_code, $testname, $section)
    {
        $collection = $this->conn->marks;
        $faculty_id = $this->faculty_id;

        // echo "Get Marks " . $faculty_id . " " . $batch . " " . $semester . " " . $subject_code . " " . $testname . " " . $section;

        try {
            // Query the collection to fetch marks based on the criteria
            $result = $collection->findOne(
                [
                    'faculty_id' => $this->faculty_id,
                    'batch' => $batch,
                    'semester' => $semester,
                    'subject_code' => $subject_code,
                    'test_name' => $testname,
                    'section' => $section
                ],
                [
                    'projection' => ['_id' => 0, 'marks' => 1, 'reg_no' => 1, 'student_name' => 1]
                ]
            );

            // Log the results for debugging (remove or disable in production)
            if ($result) {
                error_log('Marks retrieved successfully');
            } else {
                $result = [];
                error_log('No marks found for the specified criteria.');
            }

            // Return the result or false if no data is found
            return $result;
        } catch (Exception $e) {
            // Log the error with more details for troubleshooting
            error_log('Error fetching marks: ' . $e->getMessage());

            return false;
        }
    }



    /**
     * Fetch students assigned to a faculty and subject.
     */
    public function getAssignedStudents($subjectCode)
    {
        $classCollection = $this->conn->classes;
        $enrollmentCollection = $this->conn->enrollments;
        $studentCollection = $this->conn->students;

        $facultyId = $this->faculty_id;

        try {
            $class = $classCollection->findOne([
                'faculty_id' => $facultyId,
                'subject_code' => $subjectCode
            ]);

            if (!$class) {
                throw new Exception('Class not found.');
            }

            $enrollments = $enrollmentCollection->find([
                'batch' => $class['batch'],
                'semester' => $class['semester'],
                'subject_code' => $class['subject_code'],
                'section' => ['$in' => $class['student_sections']],
                'year' => $class['year']
            ], [
                'sort' => ['student_id' => 1]
            ]);

            $studentIds = array_map(fn($enrollment) => $enrollment['student_id'], iterator_to_array($enrollments));

            $studentsCursor = $studentCollection->find([
                'reg_no' => ['$in' => $studentIds]
            ]);

            $students = [];
            foreach ($studentsCursor as $student) {
                $students[] = [
                    'name' => $student['name'],
                    'reg_no' => $student['reg_no']
                ];
            }

            return $students ?: throw new Exception('No students found.');
        } catch (Exception $e) {
            error_log('Error fetching students: ' . $e->getMessage());
            return false;
        }
    }
}
