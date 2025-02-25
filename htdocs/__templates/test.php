<div class="container">
    <h2>Create Contest</h2>
    <form id="contestForm">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" id="title" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" id="description" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Contest Type</label>
            <select class="form-control" id="contestType" required>
                <option value="individual">Individual</option>
                <option value="team">Team</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Total Rounds</label>
            <input type="number" class="form-control" id="totalRounds" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Start Time</label>
            <input type="datetime-local" class="form-control" id="startTime" required>
        </div>
        <div class="mb-3">
            <label class="form-label">End Time</label>
            <input type="datetime-local" class="form-control" id="endTime" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Registration Deadline</label>
            <input type="datetime-local" class="form-control" id="registrationDeadline" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Faculty ID</label>
            <input type="text" class="form-control" id="facultyId" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Coordinators (Comma Separated)</label>
            <input type="text" class="form-control" id="coordinators">
        </div>
        <button type="submit" class="btn btn-primary">Create Contest</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#contestForm').on('submit', function(e) {
            e.preventDefault();

            const contestData = {
                title: $('#title').val(),
                description: $('#description').val(),
                contestType: $('#contestType').val(),
                totalRounds: parseInt($('#totalRounds').val()),
                startTime: $('#startTime').val(),
                endTime: $('#endTime').val(),
                registrationDeadline: $('#registrationDeadline').val(),
                facultyId: $('#facultyId').val(),
                coordinators: $('#coordinators').val().split(',').map(c => c.trim())
            };

            $.ajax({
                url: '/api/contest/create',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(contestData),
                success: function(response) {
                    alert('Contest Created Successfully!');
                    $('#contestForm')[0].reset();
                },
                error: function(err) {
                    alert('Error creating contest.');
                }
            });
        });
    });
</script>