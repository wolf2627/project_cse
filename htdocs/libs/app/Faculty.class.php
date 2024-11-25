<?php

use MongoDB\Model\BSONArray;

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


    public function getClassId($batch, $semester, $subject_code, $section, $department)
    {
        $collection = $this->conn->classes;

        try {
            $cursor = $collection->findOne(
                [
                    'faculty_id' => $this->faculty_id,
                    'batch' => $batch,
                    'semester' => $semester,
                    'subject_code' => $subject_code,
                    'section' => $section,
                    'department' => $department
                ],
                ['projection' => ['_id' => 1]]
            );

            if($cursor) {
                $result = (string)$cursor->_id;
            } else {
                $result = false;
            }

            return $result;
        } catch (Exception $e) {
            error_log('Error fetching classes: ' . $e->getMessage());
            return false;
        }
    }


    public function getBatches()
    {
        $collection = $this->conn->classes;

        try {
            $cursor = $collection->distinct('batch', ['faculty_id' => $this->faculty_id]);

            $cursor = $cursor;

            $batches = array_map(fn($batch) => ['batch' => $batch], $cursor);
            return $cursor ?: false;
        } catch (Exception $e) {
            error_log('Error fetching batches: ' . $e->getMessage());
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
                ['projection' => ['subject_code' => 1, 'batch' => 1, '_id' => 0]]
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

            $batches = $this->getBatches();
            if (isset($batches['error'])) {
                throw new Exception($batches['error']);
            }

            $cursor = $testCollection->find([
                'status' => 'active',
                'batch' => ['$in' => $batches],
                'subjects.subject_code' => ['$in' => $subjectCodes]
            ]);

            $result = [];
            foreach ($cursor as $test) {
                $testName = $test['testname'];

                // Convert BSONArray to a PHP array
                $subjectsArray = isset($test['subjects']) ? (array)$test['subjects'] : [];

                // Ensure $subjectsArray is a PHP array before applying array_filter
                if (is_array($subjectsArray)) {
                    $testSubjects = array_column(
                        array_filter(
                            $subjectsArray,
                            fn($subject) => in_array($subject['subject_code'], $subjectCodes)
                        ),
                        'subject_code'
                    );

                    // Add batch and semester details
                    $result[$testName] = [
                        'subjects' => $testSubjects,
                        'batches' => [$test['batch']],
                        'department' => $test['department'],
                        'maxmark' => $test['totalmarks'],
                        'passmark' => $test['passmarks'],
                        'semesters' => [$test['semester'] ?? 'Unknown'] // Add semester if available
                    ];
                }
            }

            return $result ?: throw new Exception('No tests found.');
        } catch (Exception $e) {
            error_log('Error fetching tests: ' . $e->getMessage());
            return false;
        }
    }


    public function getMarks($batch, $semester, $subject_code, $testname, $section, $department)
    {
        $collection = $this->conn->marks;
        $faculty_id = $this->faculty_id;

        // echo "Get Marks " . $faculty_id . " " . $batch . " " . $semester . " " . $subject_code . " " . $testname . " " . $section;

        $classCollection = $this->conn->classes;

        try {
            // Query the collection to fetch marks based on the criteria

            // TODO: Try to optimize this query to reduce the number of queries (use aggregation)

            $findClass = $classCollection->findOne(
                [
                    'faculty_id' => $faculty_id,
                    'batch' => $batch,
                    'semester' => $semester,
                    'department' => $department,
                    'subject_code' => $subject_code,
                    'section' => $section
                ],
                [
                    'projection' => ['_id' => 1]
                ]
            );

            if ($findClass) {
                // Access the _id of the found class
                $classId = $findClass->_id;

                // Find marks associated with the class_id
                $result = $collection->findOne(
                    ['class_id' => $classId, 'test_name' => $testname],
                );

                // Log the results for debugging (remove or disable in production)
                if ($result) {
                    error_log('Marks retrieved successfully');
                } else {
                    $result = [];
                    error_log('No marks found for the specified criteria.');
                }
            } else {
                $result = [];
                error_log('No class found for the specified criteria.');
            }


            // Return the result or false if no data is found
            return $result;
        } catch (Exception $e) {
            // Log the error with more details for troubleshooting
            error_log('Error fetching marks: ' . $e->getMessage());

            return false;
        }
    }

    public function updateMarks($reg_no, $new_mark, $batch, $semester, $subject_code, $testname, $section, $department)
    {
        $collection = $this->conn->marks;

        try {
            // Find the document with the given criteria
            $existing = $collection->findOne([
                'faculty_id' => $this->faculty_id,
                'batch' => $batch,
                'semester' => $semester,
                'department' => $department,
                'subject_code' => $subject_code,
                'test_name' => $testname,
                'section' => $section
            ]);

            if (!$existing) {
                throw new Exception('Marks record not found.');
            }

            // Use the positional operator to update the student's mark
            $result = $collection->updateOne(
                [
                    'faculty_id' => $this->faculty_id,
                    'batch' => $batch,
                    'semester' => $semester,
                    'subject_code' => $subject_code,
                    'department' => $department,
                    'test_name' => $testname,
                    'section' => $section,
                    'marks.reg_no' => $reg_no  // Match student by reg_no inside the marks array
                ],
                [
                    '$set' => [
                        'marks.$.marks' => $new_mark  // Update the specific student's marks
                    ]
                ]
            );

            // Return true if one document was modified
            return $result->getModifiedCount() > 0;
        } catch (Exception $e) {
            // Log and throw the error
            error_log('Error updating marks: ' . $e->getMessage());
            throw new Exception('Error updating marks: ' . $e->getMessage());
        }
    }



    public function enterMark($batch, $semester, $subject_code, $testname, $section, $marks, $department)
    {
        $collection = $this->conn->marks;

        foreach ($marks as &$mark) {
            $mark['marks'] = (int)$mark['marks'];
        }

        try {
            // Check if a record for this test already exists

            $classId = $this->getClassId($batch, $semester, $subject_code, $section, $department);
            $testId = $this->getTestId($testname, $batch, $semester, $subject_code, $department);


            $existingData = [
                'class_id' => new MongoDB\BSON\ObjectId($classId),
                'test_id' => new MongoDB\BSON\ObjectId($testId),
            ];


            $existing = $collection->findOne($existingData);

            if ($existing) {
                return 'duplicate';
            }

            // Transform marks into an array of structured objects
            $structuredMarks = [];
            foreach ($marks as $key => $student) {
                $structuredMarks[] = [
                    'reg_no' => $student['reg_no'],
                    'studentname' => $student['studentname'],
                    'marks' => $student['marks']
                ];
            }

            // Prepare the document to be inserted

            $finaldata = [
                'test_id' => new MongoDB\BSON\ObjectId($testId), //converting to object id
                'class_id' =>  new MongoDB\BSON\ObjectId($classId), //converting to object id
                'marks' => $structuredMarks,
                'Entered_at' => date('Y-m-d H:i:s')
            ];

            // Insert the document into the collection
            $collection->insertOne($finaldata);

            return true;
        } catch (Exception $e) {
            error_log('Error entering marks: ' . $e->getMessage());
            return false;
        }
    }


    public function getTestId($testname, $batch, $semester, $subject_code, $department)
    {
        $collection = $this->conn->tests;

        try {
            $cursor = $collection->findOne(
                [
                    'testname' => $testname,
                    'batch' => $batch,
                    'semester' => $semester,
                    'subjects.subject_code' => $subject_code,
                    'department' => $department
                ],
                ['projection' => ['_id' => 1]]
            );

            if ($cursor) {
                // Correctly access the _id field as a property
                $result = (string)$cursor->_id; // Convert ObjectId to string
            } else {
                $result = false; // Return false if no document is found
            }

            return $result;
        } catch (Exception $e) {
            error_log('Error fetching tests: ' . $e->getMessage());
            return false; // Return false if an exception occurs
        }
    }



    /**
     * Fetch students assigned to a faculty and subject.
     */
    public function getAssignedStudents($subjectCode, $batch, $semester)
    {
        $classCollection = $this->conn->classes;
        $enrollmentCollection = $this->conn->enrollments;
        $studentCollection = $this->conn->students;

        $facultyId = $this->faculty_id;


        try {
            $class = $classCollection->findOne([
                'faculty_id' => $facultyId,
                'subject_code' => $subjectCode,
                'batch' => $batch,
                'semester' => $semester
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
