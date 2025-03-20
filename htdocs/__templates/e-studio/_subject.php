<?php
// Retrieve subject code from the query string
$subject_code = $_GET['subject_code'] ?? ''; // Default to an empty string if no code is passed

// Example content for each subject
$subject_files = [
    'subject1' => [
        'type' => 'pdf', // Assignment PDF file
        'file' => 'files/CW_666e516de9d81/chapter_6_anatomy_of_flowering_plants.pdf',
        'title' => 'Anatomy of Flowering Plants'
    ],
    'subject2' => [
        'type' => 'pdf', // Assignment PDF file
        'file' => 'files/CW_666e516de9d81/chapter_5_morphology_of_flowering_plants.pdf',
        'title' => 'Morphology of Flowering Plants'
    ],
    'subject3' => [
        'type' => 'video', // YouTube video link
        'file' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'title' => 'Structural Organisation of Animals'
    ],
    'subject4' => [
        'type' => 'pdf', // PDF file
        'file' => 'files/CW_666e516de9d81/chapter_17_breathing_and_exchange_of_gases.pdf',
        'title' => 'Breathing and Exchange of Gases'
    ],
    'subject5' => [
        'type' => 'pdf', // PDF file
        'file' => 'files/CW_666e516de9d81/chapter_15_body_fluids_and_circulation.pdf',
        'title' => 'Body Fluids and Circulation'
    ],
    'subject6' => [
        'type' => 'pdf', // PDF file
        'file' => 'files/CW_666e516de9d81/chapter_16_excretory_products.pdf',
        'title' => 'Excretory Products and Their Elimination'
    ],
    'subject7' => [
        'type' => 'pdf', // PDF file
        'file' => 'files/CW_666e516de9d81/chapter_17_locomotion_and_movement.pdf',
        'title' => 'Locomotion and Movement'
    ],
    'subject8' => [
        'type' => 'video', // YouTube video link
        'file' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'title' => 'Neural Control and Coordination'
    ]
];

// Check if the subject code exists
if (!array_key_exists($subject_code, $subject_files)) {
    echo "Subject not found.";
    exit;
}

// Fetch the content for the selected subject
$subject = $subject_files[$subject_code];

?>
    <div class="container mt-5">
        <h2><?php echo $subject['title']; ?></h2>
        
        <?php if ($subject['type'] == 'pdf'): ?>
            <h4>Assignment PDF</h4>
            <a href="<?php echo $subject['file']; ?>" target="_blank" class="btn btn-primary">Download PDF</a>
        <?php elseif ($subject['type'] == 'video'): ?>
            <h4>Lecture Video</h4>
            <a href="<?php echo $subject['file']; ?>" target="_blank" class="btn btn-danger">Watch Video</a>
        <?php endif; ?>
        
    </div>
