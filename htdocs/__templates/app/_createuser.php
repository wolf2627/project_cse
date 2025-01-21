<section class="py-5 container">

    <h2 class="text-center mb-4">User Creation</h2>

    <div class="input-group mb-4">
        <label class="form-label fw-bold me-3">User Type:</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="userType" id="singleUser" value="single" disabled>
            <label class="form-check-label" for="singleUser">Single User</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="userType" id="multipleUsers" value="multiple" checked>
            <label class="form-check-label" for="multipleUsers">Multiple Users</label>
        </div>
    </div>

    <div class="input-group mb-4">
        <label class="form-label fw-bold me-3">Role:</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="role" id="student" value="student">
            <label class="form-check-label" for="student">Student</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="role" id="faculty" value="faculty" checked>
            <label class="form-check-label" for="faculty">Faculty</label>
        </div>
    </div>

    <div class="mb-4">
        <label for="formFile-usercreate" class="form-label">Upload Excel File</label>
        <input class="form-control" type="file" id="formFile-usercreate" accept=".xlsx, .xls">
    </div>


    <div class="d-flex justify-content-start">
        <button class="btn btn-success me-3" id="create-users">Create User</button>
        <button class="btn btn-secondary" id="create-users-clear">Clear</button>
    </div>
</section>