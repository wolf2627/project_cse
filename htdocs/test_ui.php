<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm p-4" style="width: 100%; max-width: 400px; border-radius: 15px;">
            <div class="text-center mb-4">
                <h1 class="h4 fw-bold text-primary">Welcome Back</h1>
                <p class="text-muted">Log in to access your account</p>
            </div>
            <form>
                <div class="form-floating mb-3">
                    <input name="email" type="text" class="form-control" id="floatingInput" placeholder="name@example.com" required>
                    <label for="floatingInput"><i class="bi bi-person me-2"></i>Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                    <label for="floatingPassword"><i class="bi bi-lock me-2"></i>Password</label>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember Me</label>
                    </div>
                    <a href="#" class="text-decoration-none small text-primary">Forgot Password?</a>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Log in</button>
                <p class="text-center text-muted mt-3 mb-0">Don't have an account? 
                    <a href="#" class="text-primary text-decoration-none">Sign up</a>
                </p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
