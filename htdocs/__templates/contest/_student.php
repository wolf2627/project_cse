
<?php

use Carbon\Carbon;

$studentId = Session::getUser()->getRegNo();
$contest = new Contest($contestId);
$rounds = $contest->getRounds();

if(!$rounds) {
    echo '<div class="alert alert-info">No rounds available</div>';
    return;
}

$currentTime = date("Y-m-d H:i:s");

?>

<div class="mt-4">
    <div class="row">
        <?php foreach ($rounds as $round): 
            $startTime = Carbon::parse($round['start_time']);
            $endTime = Carbon::parse($round['end_time']);

            if ($currentTime < $startTime) {
                $status = "Upcoming";
                $statusClass = "bg-secondary";
                $button = '<button class="btn btn-sm btn-secondary" disabled>Not Started</button>';
            } elseif ($currentTime >= $startTime && $currentTime <= $endTime) {
                $status = "Ongoing";
                $statusClass = "bg-success";
                $button = '<a href="/contest/round/' . $round['_id'] . '" class="btn btn-sm btn-primary">Enter Round</a>';
            } else {
                $status = "Completed";
                $statusClass = "bg-danger";
                $button = '<button class="btn btn-sm btn-dark" disabled>Completed</button>';
            }
        ?>
            <div class="col-md-6 mb-4">
                <div class="card border-info shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><?= htmlspecialchars($round['name']) ?></h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Round Number:</strong> <?= htmlspecialchars($round['round_number']) ?></p>
                        <p><strong>Start Time:</strong> <?= htmlspecialchars($startTime) ?></p>
                        <p><strong>End Time:</strong> <?= htmlspecialchars($endTime) ?></p>
                        <p><strong>Status:</strong> <span class="badge <?= $statusClass ?>"><?= $status ?></span></p>
                        <?= $button ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

