<div class="container">

    <h1 class="text-center bg-info">E-Studio</h1>
    <p class="text-center">E-Studio offers state-of-the-art video recording facilities to create high-quality video lectures. These videos are uploaded to our channel for wider reach and enhanced learning</p>

    <?php
    $links = [
        [
            'link' => 'https://www.youtube.com/embed/zt9oRh6S1ag?si=s4It24FI1S6L1adg',
            'title' => 'ROLE OF COMPUTATIONAL INTELLIGENCE IN CLINICAL DECISION MAKING',
            'description' => 'By Dr.D.Shanthi'
        ],
        [
            'link' => 'https://www.youtube.com/embed/Y5bEpOHSWOs?si=PRT-_BodTBJZkX1r',
            'title' => 'CIPHER TECHNIQUES',
            'description' => 'By Dr.N.UmaMaheshwari'
        ],
        [
            'link' => 'https://www.youtube.com/embed/Sw_GVd_dUOk?si=6ZKjupWjWYaFt0UR',
            'title' => 'UNIX INTERNALS-DATASTRUCTURES',
            'description' => 'By Dr.S.Puspalatha'
        ],
        [
            'link' => 'https://www.youtube.com/embed/xv78MZ1Phi8?si=-tzu3W7SFxJNQnGJ',
            'title' => 'CPU SCHEDULING',
            'description' => 'By Dr.DhanaLakshmi'
        ],
        [
            'link' => 'https://www.youtube.com/embed/k-eysAsdPKM?si=v82JkO_DaeS5Oiin',
            'title' => 'DATA STRUCTURES & ALGORITHMS',
            'description' => 'By Dr.K.ManiVannan'
        ],
        [
            'link' => 'https://www.youtube.com/embed/zuqUwYkECN8?si=TVc1O5K_bHgMGVIk',
            'title' => 'UNIFIED APPROCH-METHODOLOGY FOR SOFTWARE DEVELOPMENT',
            'description' => 'By Dr.M.S.Thanabal'
        ],
        [
            'link' => 'https://www.youtube.com/embed/e80eKe4c0TI?si=9gnObipPPMXypFIQ',
            'title' => 'ERROR DETECTION & CORRECTION',
            'description' => 'By Dr.S.Satheesbabu'
        ]
    ];
    ?>

    <div class="row mt-4">
        <!-- Loop through the links array to create a card for each video -->
        <?php foreach ($links as $link): ?>
            <div class="col-md-3 mb-4">
                <div class="card d-flex flex-column" style="height: 100%; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div class="ratio ratio-16x9" style="flex-grow: 1;">
                        <!-- Dynamically insert the video link -->
                        <iframe src="<?php echo $link['link']; ?>"
                            title="YouTube video player"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen></iframe>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title"><?php echo $link['title']; ?></h6>
                        <p class="card-text"><?php echo $link['description']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Hover effect using inline styles -->
<script>
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            card.style.transform = 'scale(1.05)';
            card.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.1)';
        });
        card.addEventListener('mouseleave', function() {
            card.style.transform = 'scale(1)';
            card.style.boxShadow = 'none';
        });
    });
</script>