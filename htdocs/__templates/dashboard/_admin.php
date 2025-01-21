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
            </div>
            <div class="card-body">
                <p> Role: <?= $data['1'] ?></p>
                <p> Email: <?= $data['2'] ?></p>
            </div>
        </div>
    </div>
</div>