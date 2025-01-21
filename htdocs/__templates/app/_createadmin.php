<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h3>Create an Admin Account</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group mb-4">
                            <label for="admin-username" class="form-label">Name</label>
                            <input type="text" class="form-control" id="admin-username" placeholder="Enter name" autofocus required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="admin-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="admin-email" placeholder="Enter email" required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="admin-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="admin-password" placeholder="Enter password" required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="admin-confirm-password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="admin-confirm-password" placeholder="Re-enter password" required>
                        </div>

                        <div class="form-group mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="show-password">
                            <label class="form-check-label" for="show-password">Show Password</label>
                        </div>

                        <script>
                            document.getElementById('show-password').addEventListener('change', function() {
                                var passwordField = document.getElementById('admin-password');
                                var confirmPasswordField = document.getElementById('admin-confirm-password');
                                if (this.checked) {
                                    passwordField.type = 'text';
                                    confirmPasswordField.type = 'text';
                                } else {
                                    passwordField.type = 'password';
                                    confirmPasswordField.type = 'password';
                                }
                            });
                        </script>

                        <div class="d-grid">
                            <button type="button" id="admin-create-account-btn" class="btn btn-primary btn-block">Create Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>