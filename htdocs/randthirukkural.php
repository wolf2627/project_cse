<?php
// Path to the transformed JSON file
$jsonFile = 'required/files/transformed_thirukkural.json';

// Read the JSON file
if (!file_exists($jsonFile)) {
    die('Error: JSON file not found.');
}

$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

// Ensure the JSON structure is valid
if (!$data) {
    die('Error: Invalid JSON structure.');
}

// Generate a random number based on the range of available entries
$randomNumber = rand(1, count($data));

// Fetch the random entry
$randomKural = $data[$randomNumber];

// Display the random entry on the web
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Random Thirukkural</title>
    <!-- Google Fonts for Tamil and English -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Tamil:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }
        .kural-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .title {
            font-size: 1.5em;
            color: #333;
            text-align: center;
        }
        .content {
            margin-top: 20px;
            line-height: 1.8;
        }
        .number {
            text-align: center;
            font-size: 1.2em;
            color: #666;
        }
        .line {
            font-family: 'Noto Sans Tamil', serif;
            font-size: 1.2em;
            color: #444;
        }
        .translation, .explanation {
            font-size: 1em;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="kural-container">
        <div class="title">Random Thirukkural</div>
        <div class="number">Kural Number: <?= $randomKural['Number'] ?></div>
        <div class="content">
            <p class="line"><?= $randomKural['Line1'] ?></p>
            <p class="line"><?= $randomKural['Line2'] ?></p>
            <p class="translation"><strong>Translation:</strong> <?= $randomKural['Translation'] ?></p>
            <p class="explanation"><strong>Explanation:</strong> <?= $randomKural['explanation'] ?></p>
        </div>
    </div>
</body>
</html>
