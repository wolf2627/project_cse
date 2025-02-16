<?php

$facultyId = Session::getUser()->getFacultyId();
$contest = new Contest($contestId);
$isCoordinator = $contest->isCoordinator($facultyId);
$registrations = ContestRegistration::showRegistrations($contestId);

?>

<div class="container mt-4">
    <div class="row">
        <!-- Contest Info Panel -->
        <div class="col-lg-6 mb-4">
            <div class="card border-primary shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Contest Details</h5>
                </div>
                <div class="card-body">
                    <h5 class="text-primary"><?= htmlspecialchars($contest->getTitle()) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($contest->getDescription()) ?></p>
                    <hr>
                    <p><i class="far fa-calendar-alt"></i> <strong>Start:</strong> <?= $contest->getStartTime() ?></p>
                    <p><i class="far fa-calendar-alt"></i> <strong>End:</strong> <?= $contest->getEndTime() ?></p>
                    <p><i class="far fa-calendar-alt"></i> <strong>Registration Deadline:</strong> <?= $contest->getRegistrationDeadline() ?></p>
                </div>
            </div>
        </div>

        <!-- Approve Students Panel -->
        <?php if ($isCoordinator): ?>
            <div class="col-lg-6 mb-4">
                <div class="card border-warning shadow-sm">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Student Approvals</h5>
                        <span class="badge bg-dark" id="pending-count" data-pending-count=<?= count($registrations) ?>><?= count($registrations) ?> Pending</span>
                    </div>
                    <div class="card-body">
                        <?php if (count($registrations) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($registrations as $registration): ?>
                                            <tr id="row-<?= htmlspecialchars($registration['student_id']) ?>">
                                                <td><strong><?= htmlspecialchars($registration['student_id']) ?></strong></td>
                                                <td>
                                                    <span id="status-<?= htmlspecialchars($registration['student_id']) ?>" class="badge <?= $registration['status'] === 'approved' ? 'bg-success' : 'bg-secondary' ?>">
                                                        <?= htmlspecialchars($registration['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($registration['status'] !== 'approved'): ?>
                                                        <button class="btn btn-sm btn-success approve-btn"
                                                            data-contest-id="<?= htmlspecialchars($contestId) ?>"
                                                            data-student-id="<?= htmlspecialchars($registration['student_id']) ?>">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-success">Approved</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center">No students pending approval.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>