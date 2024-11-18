<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="container mt-5">
        <h2>Enter Student Marks</h2>
        <form id="studentMarksForm" method="POST" action="/test">
            <table class="table table-hover table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Reg. No.</th>
                        <th>Student Name</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    print_r($data);
                    // Create a new Faculty object
                    $faculty = new Faculty();
                    // Fetch student data from the database
                    $students = $faculty->getAssignedStudents($data[1]);

                    if (!empty($students)) {
                        $index = 1;
                        foreach ($students as $student) {
                            echo "<tr>
                                    <td>{$index}</td>
                                    <td>
                                        <input type='text' name='students[{$index}][reg_no]' class='form-control' value='{$student['reg_no']}' hidden>
                                        {$student['reg_no']}
                                    </td>
                                    <td>
                                        <input type='text' name='students[{$index}][studentname]' class='form-control' value='{$student['name']}' hidden>
                                        {$student['name']}
                                    </td>
                                    <td>
                                        <input type='number' name='students[{$index}][marks]' class='form-control' placeholder='Enter Marks' required>
                                    </td>
                                  </tr>";
                            $index++;
                        }
                    } else {
                        echo "<tr>
                                <td colspan='4' class='text-center'>No students found</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</main>