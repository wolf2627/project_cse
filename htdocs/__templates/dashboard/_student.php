<div class="container mt-5">
    <? // print_r($data);
    ?>
    <!-- Row with justify-content-center to center the card -->
    <div class="row">
        <!-- Column for the card with increased padding and centered card -->
        <div class="col-md-6">
            <!-- Card Component with extra padding and margin -->
            <div class="card p-4" id="info-card" style="width: 100%; max-width: fit-content;">
                <div class="card-body">
                    <h5 class="card-title">Welcome, <?= $data['0'] ?></h5>
                    <h6 class="card-subtitle mb-3 text-muted"><?= $data['1'] ?> - <?= $data['8'] ?></h6>
                    <p class="card-text">Register Number: <?= $data['2'] ?></p>
                    <p class="card-text">Roll Number: <?= $data['4'] ?></p>
                    <p class="card-text">Department: <?= $data['7'] ?></p>
                    <p class="card-text">Semester: <?= $data['5'] ?></p>
                    <p class="card-text">Email: <?= $data['3'] ?></p>
                </div>
            </div>
        </div>
    </div>
</div>