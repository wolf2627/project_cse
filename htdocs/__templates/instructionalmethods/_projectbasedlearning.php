<style>
    .report-item {
        background-color: #17a2b8; /* bg-info equivalent */
        padding: 10px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .report-item:hover {
        transform: scale(1.03);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    .serial-number {
        width: 40px;
        text-align: center;
        font-weight: bold;
        /* background-color: #007bff; More distinct color for serial number */
        padding: 10px;
        color: white;
    }

    .report-content {
        flex-grow: 1;
        padding-left: 10px;
    }

    a {
        text-decoration: none;
        color: white; /* Adjusted to white for better contrast on bg-info */
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container">
    <h4 class="text-center mb-4 h4">Project Based Learning</h4>

    <?php
    $links = [
        [
            'link' => 'https://www.youtube.com/embed/Ce7GAuJBRUs?si=E8BW-0tRqEuyCtdq',
            'title' => 'Sample 1',
            'description' => 'By'
        ],
        [
            'link' => 'https://www.youtube.com/embed/pZ9GtZIjyY0?si=aQggiVWv62GO44Aw',
            'title' => 'Sample 2',
            'description' => 'By'
        ],
        [
            'link' => 'https://www.youtube.com/embed/TUgZD6RSnb4?si=DxcYb_QqB2XSJpoG',
            'title' => 'Sample 3',
            'description' => 'By'
        ],
    ];
    ?>

    <div class="row mt-4">
        <?php foreach ($links as $link): ?>
            <div class="col-md-4 mb-4">
                <div class="card d-flex flex-column h-100">
                    <div class="ratio ratio-16x9 flex-grow-1">
                        <iframe src="<?php echo $link['link']; ?>"
                            title="YouTube video player"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <hr>

    <div class="mt-4 d-flex">
        <div class="w-50">
            <div class="row">
                <?php
                $reports = [
                    ['year' => '2024-25', 'file' => '2024-25 PBL report.pdf'],
                    ['year' => '2023-24', 'file' => '2023-2024 PBL report.pdf'],
                    ['year' => '2022-23', 'file' => '2022-23 PBL report.pdf'],

                ];
                foreach ($reports as $index => $report): ?>
                    <div class="col-12 report-item" onclick="window.open('/required/uploads/catergorypdf/<?php echo $report['file']; ?>', '_blank')">
                        <div class="serial-number"><?php echo $index + 1; ?>.</div>
                        <div class="report-content">
                            <a href="/required/uploads/catergorypdf/<?php echo $report['file']; ?>" target="_blank"><?php echo $report['year']; ?> Report</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
