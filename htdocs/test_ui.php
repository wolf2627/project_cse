<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Questions to Contest Round</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="container mt-5">
    <h2>Add Questions to Contest Round</h2>
    
    <form id="addQuestionsForm">
        <div class="mb-3">
            <label for="contestId" class="form-label">Select Contest</label>
            <select id="contestId" name="contestId" class="form-select" required>
                <option value="">-- Select Contest --</option>
                <!-- Options will be loaded dynamically -->
            </select>
        </div>
        
        <div class="mb-3">
            <label for="roundNumber" class="form-label">Round Number</label>
            <input type="number" id="roundNumber" name="roundNumber" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label for="questionIds" class="form-label">Select Questions</label>
            <select id="questionIds" name="questionIds[]" class="form-select" multiple required>
                <!-- Questions will be loaded dynamically -->
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Questions</button>
    </form>
    
    <script>
        $(document).ready(function () {
            // Load contests
            $.get("fetch_contests.php", function (data) {
                let contests = JSON.parse(data);
                contests.forEach(contest => {
                    $("#contestId").append(new Option(contest.name, contest._id));
                });
            });
            
            // Load questions
            $.get("fetch_questions.php", function (data) {
                let questions = JSON.parse(data);
                questions.forEach(question => {
                    $("#questionIds").append(new Option(question.Question, question._id));
                });
            });
            
            // Handle form submission
            $("#addQuestionsForm").submit(function (e) {
                e.preventDefault();
                
                let formData = $(this).serialize();
                
                $.post("add_questions_to_round.php", formData, function (response) {
                    alert(response.message);
                }, "json");
            });
        });
    </script>
</body>
</html>