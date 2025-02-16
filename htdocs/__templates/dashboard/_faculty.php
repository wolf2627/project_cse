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

            <div class="col-md-4 mb-3">
                <!-- Left edge box with title -->
                <div class="card border-primary">
                    <div class="card-header text-white bg-primary">
                        <strong>Welcome, <?= $data['0'] ?> </strong>
                    </div>
                    <div class="card-body">
                        <p> Role: <?= $data['1'] ?></p>
                        <p class="card-text">Designation: <?= $data['4'] ?></p>
                        <p class="card-text">Id: <?= $data['5'] ?></p>
                        <p class="card-text">Department: <?= $data['3'] ?></p>
                        <p class="card-text">Email: <?= $data['2'] ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card border-primary h-100">
                    <div class="card-header text-white bg-primary">
                        <strong>Contests</strong>
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
                                            <strong>
                                                <a href="/contest?contest_id=<?= htmlspecialchars($contest['_id']) ?>" class="text-primary">
                                                    <?= htmlspecialchars($contest['title']) ?>
                                                </a>
                                            </strong>
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
                                        <a href="/contest?contestid=<?=base64_encode(htmlspecialchars($contest['_id'])) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="card-text">No upcoming contests.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>