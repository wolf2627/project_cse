<!-- Marquee -->
<div>
    <div class="row">
        <div class="col-md-12">
            <!-- Marquee -->
            <marquee behavior="scroll" direction="left" class="text-warning small p-2 border rounded">
                <strong>Welcome to the site. This Site Is Under Development.</strong>
            </marquee>
        </div>
    </div>

    <div class="mt-3">
        <div class="row" data-masonry='{"percentPosition": true }'>

            <!-- Welcome Card -->
            <div class="col-md-4 mb-3">
                <div class="card border-primary">
                    <div class="card-header text-white bg-primary">
                        <strong>Welcome, <?= $data['0'] ?> </strong>
                        <h6 class="card-subtitle text-muted"><?= $data['8'] ?></h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Role:</strong> <?= $data['1'] ?></p>
                        <p><strong>Register Number:</strong> <?= $data['2'] ?></p>
                        <p><strong>Roll Number:</strong> <?= $data['4'] ?></p>
                        <p><strong>Department:</strong> <?= $data['7'] ?></p>
                        <p><strong>Semester:</strong> <?= $data['5'] ?></p>
                        <p><strong>Email:</strong> <?= $data['3'] ?></p>
                    </div>
                </div>
            </div>

            <? if (false): ?>
                <!-- Enrolled Subjects Card -->
                <div class="col-md-4 mb-3">
                    <div class="card border-primary">
                        <div class="card-header text-white bg-primary">
                            <strong>Enrolled Subjects</strong>
                        </div>
                        <div class="card-body">
                            <?php

                            $student = new Student(Session::getUser()->getRegNo());
                            $enrolledSubjects = $student->getEnrolledClasses();

                            // Convert subjects array into an associative array indexed by subject_code
                            $subject_maps = [];
                            foreach (Essentials::loadSubjects() as $subject) {
                                $subject_maps[$subject['subject_code']] = $subject;
                            }
                            ?>
                            <?php if (count($enrolledSubjects) > 0): ?>
                                <ul class="list-group">
                                    <?php foreach ($enrolledSubjects as $subject): ?>
                                        <li class="list-group-item">
                                            <?php
                                            $subject_code = $subject['subject_code'];
                                            $subject_name = isset($subject_maps[$subject_code]) ? $subject_maps[$subject_code]['subject_name'] : 'Unknown Subject';
                                            ?>
                                            <strong><?= $subject_code ?>:</strong> <?= $subject_name ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="card-text">No subjects enrolled yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Time Table Card -->
                <div class="col-md-4 mb-3">
                    <div class="card border-primary shadow-sm">
                        <div class="card-header bg-primary text-white text-center">
                            <strong><i class="fas fa-calendar-alt"></i> Time Table (today)</strong>
                        </div>
                        <div class="card-body">
                            <?php
                            $tt = new TimeTable();
                            $todaytt = $tt->getStudentTimeTableByDate(Session::getUser()->getRegNo());
                            ?>

                            <?php if (count($todaytt) > 0): ?>
                                <ul class="list-group">
                                    <?php foreach ($todaytt as $entry): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-book-open text-primary"></i>
                                                <strong><?= $entry['subject_code'] ?></strong>
                                            </div>
                                            <span class="badge bg-success rounded-pill">
                                                <i class="far fa-clock"></i> <?= $entry['time'] ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="card-text text-center text-muted">
                                    <i class="fas fa-info-circle"></i> No classes today.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>


                <!-- Announcements Card -->
                <div class="col-md-4 mb-3">
                    <div class="card border-primary">
                        <div class="card-header text-white bg-primary">
                            <strong>Announcements</strong>
                        </div>
                        <div class="card-body">
                            <p class="card-text">No announcements yet.</p>
                        </div>
                    </div>
                </div>
            <? endif; ?>


            <!-- Upcoming Contests Card -->
            <div class="col-md-4 mb-3">
                <div class="card border-primary">
                    <div class="card-header text-white bg-primary">
                        <strong>Upcoming Contests</strong>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        <?php
                        $contests = Contest::showContests('upcoming', true);
                        ?>
                        <?php if (count($contests) > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($contests as $contest): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($contest['title']) ?></strong>
                                            <p class="text-muted small mb-1">
                                                <?= htmlspecialchars($contest['description']) ?>
                                            </p>
                                            <p class="text-muted small">
                                                <?php
                                                $date = new DateTime($contest['start_time'], new DateTimeZone('UTC'));
                                                $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
                                                ?>
                                                <i class="far fa-calendar-alt"></i> <?= $date->format('d-m-Y H:i:s') ?>
                                            </p>
                                        </div>
                                        <?
                                        // Check if the user is already registered for the contest
                                        $registered = ContestRegistration::isRegistered($contest['_id'], Session::getUser()->getRegNo());

                                        if ($registered) {
                                            $status = $registered;
                                            error_log("Status : " . $status);
                                            if ($status === 'approved') {
                                                $status = 'Registered and approved';
                                            } else {
                                                $status = 'Waiting for approval';
                                            }

                                        ?>
                                            <span class="badge bg-success rounded-pill" id="registration-status"><?= $status ?></span>

                                        <?

                                        } else {
                                            $status = 'Not Registered';
                                        ?>
                                            <button class="btn btn-sm btn-primary register-btn"
                                                data-contest-id="<?= htmlspecialchars($contest['_id']) ?>">
                                                <i class="fas fa-edit"></i> Register
                                            </button>
                                        <?
                                        }
                                        ?>

                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="card-text">No upcoming contests.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>




            <script>
                // Initialize Masonry after the page loads
                document.addEventListener("DOMContentLoaded", function() {
                    var grid = document.querySelector('.row[data-masonry]');
                    new Masonry(grid, {
                        itemSelector: '.col-md-4',
                        percentPosition: true
                    });
                });
            </script>



        </div>