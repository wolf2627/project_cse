<style>
    .report-item {
        background-color: #d8f3dc; /* bg-info equivalent */
        padding: 10px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, color 0.3s ease;
        border-radius: 5px; /* Added rounded corners for a smoother look */
    }

    /* Hover effect for report items */
    .report-item:hover {
        transform: scale(1.05); /* Increase scale slightly */
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2); /* Enhance shadow */
        background-color: #a8d8a7; /* Change background on hover */
        color: #fff; /* Change text color */
    }

    /* Hover effect for serial number */
    .serial-number:hover {
        background-color: #6f8f71; /* Darker background */
        color: white; /* White text */
        transform: scale(1.2); /* Slightly increase size */
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .serial-number {
        width: 40px;
        text-align: center;
        font-weight: bold;
        padding: 10px;
        color: black;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .report-content {
        flex-grow: 1;
        padding-left: 10px;
    }

    a {
        text-decoration: none;
        color: black; /* Default color */
        transition: color 0.3s ease;
    }

    /* Hover effect for links */
    a:hover {
        color: #0d6efd; /* Change link color to blue on hover */
    }

    /* Added margin to space out sections */
    .column {
        margin-right: 30px; /* Added space between the student and faculty columns */
        transition: margin 0.3s ease;
    }

    /* Hover effect for section columns */
    .column:hover {
        margin-right: 40px; /* Slightly increase spacing between columns */
    }

    /* Added space between titles and center the text */
    .section-title {
        background-color: #d8f3dc; /* bg-info equivalent */
        padding: 5px 15px;
        color: black;
        display: inline-block; /* Ensures background only applies to text */
        margin-bottom: 15px; /* Space between titles and content */
        font-size: 1.25rem;
        text-align: center; /* Center title text */
        width: 100%; /* Ensure title spans the full width of the column */
        border-radius: 5px; /* Rounded corners for title */
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    /* Hover effect for section titles */
    .section-title:hover {
        background-color: #7ec8a9; /* Lighter green on hover */
        transform: translateY(-3px); /* Slightly lift the title */
    }

    /* Centering the container and its content */
    .container {
        display: flex;
        flex-direction: column; /* Align content vertically */
        justify-content: flex-start; /* Ensure content starts from the top */
        align-items: center; /* Center horizontally */
        min-height: 100vh; /* Full height of the viewport */
    }

    /* Optional: You can also add responsiveness for smaller screens */
    @media (max-width: 768px) {
        .d-flex {
            flex-direction: column;
        }

        .column {
            margin-right: 0;
            margin-bottom: 20px; /* Add space between student and faculty sections for small screens */
        }
    }
</style>

<div class="container">
    <!-- Internships Title at the Top -->
    <h4 class="text-center mb-4 h4">Internships</h4>

    <!-- Summary Table -->
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="thead-light">
            <tr>
                <th>S.No.</th>
                <th>Academic Year</th>
                <th>Number of students gone for Internship</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $data = [
                ['year' => '2024-25', 'students' => 333],
                ['year' => '2023-24', 'students' => 303],
                ['year' => '2022-23', 'students' => 64],
                ['year' => '2021-22', 'students' => 100],
            ];
            $total = 0;
            foreach ($data as $index => $row) {
                $total += $row['students'];
                echo "<tr>
                    <td>" . ($index + 1) . "</td>
                    <td>{$row['year']}</td>
                    <td>{$row['students']}</td>
                  </tr>";
            }
            ?>
            <tr>
                <th colspan="2">Total</th>
                <th><?php echo $total; ?></th>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        <!-- Students Section -->
        <div class="column">
            <!-- <h5 class="section-title">Student</h5> -->
            <div class="row text-right">
                <?php
                // Define report data for both students and teachers
                $reports = [
                    'student' => [
                        ['year' => '2024-25', 'file' => 'intern_student_2025.pdf'],
                        ['year' => '2023-24', 'file' => 'intern_student_2024.pdf'],
                        ['year' => '2022-23', 'file' => 'intern_student_2023.pdf'],
                        ['year' => '2021-22', 'file' => 'intern_student_2022.pdf'],
                    ],
                ];

                // Function to generate report items
                function generateReportItems($reports) {
                    foreach ($reports as $index => $report): ?>
                        <div class="col-12 report-item" onclick="window.open('/required/uploads/catergorypdf/<?php echo $report['file']; ?>', '_blank')">
                            <div class="serial-number"><?php echo $index + 1; ?>.</div>
                            <div class="report-content">
                                <a href="/required/uploads/catergorypdf/<?php echo $report['file']; ?>" target="_blank"><?php echo $report['year']; ?> Report</a>
                            </div>
                        </div>
                    <?php endforeach;
                }

                // Generate student reports
                generateReportItems($reports['student']);
                ?>
            </div>
        </div>

        <!-- Teachers Section -->
        <!-- <div class="column">
            <h5 class="section-title">Faculty</h5>
            <div class="row text-right">
                <?php
                // Generate faculty reports
                // generateReportItems($reports['faculty']);
                ?>
            </div>
        </div> -->
    </div>
</div>
