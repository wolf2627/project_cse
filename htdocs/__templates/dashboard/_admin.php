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
                    <h6 class="card-subtitle mb-3 text-muted"><?= $data['1'] ?></h6>
                    <p class="card-text">Email: <?= $data['2'] ?></p>
                </div>
            </div>
        </div>
    </div>
</div>