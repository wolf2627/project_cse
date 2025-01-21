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

    <div class="col-md-2">
        <!-- Left edge box with title -->
        <div class="card border-primary">
            <div class="card-header text-white bg-primary">
                <strong>Welcome, <?= $data['0'] ?> </strong>
                <h6 class="card-subtitle text-muted"><?= $data['8'] ?></h6>
            </div>
            <div class="card-body">
                <p> Role: <?= $data['1'] ?></p>
                <p class="card-text">Register Number: <?= $data['2'] ?></p>
                <p class="card-text">Roll Number: <?= $data['4'] ?></p>
                <p class="card-text">Department: <?= $data['7'] ?></p>
                <p class="card-text">Semester: <?= $data['5'] ?></p>
                <p class="card-text">Email: <?= $data['3'] ?></p>
            </div>
        </div>
    </div>
</div>