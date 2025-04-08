<?php

if (Session::isAuthenticated()) {
    header("Location: /dashboard");
    die();
}

$login_page = true;
Session::set('mode', 'web');

if (isset($_POST['email']) and isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $result = UserSession::authenticate($email, $password);
    $login_page = false;
}

if (!$login_page) {
    if ($result) {
        Log::dolog("Login Success: $email", "LOGIN", true);
        $should_redirect = Session::get('_redirect');
        $redirect_to =  get_config('base_path') . "dashboard";
        if (isset($should_redirect)) {
            $redirect_to = $should_redirect;
            Session::set('_redirect', false);
        }
?>
        <script>
            window.location.href = "<?= $redirect_to ?>";
        </script>
    <?
    } else {
        Log::dolog("Login Failed: $email", "LOGIN", true);
    ?>
        <script>
            window.location.href = "/login?error=1";
        </script>
    <?
    }
} else {

    ?>
    <main>
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="card shadow-sm p-4 opacity-80" style="width: 100%; max-width: 400px; border-radius: 15px;">
                <div class="d-flex justify-content-center mb-4">
                    <img class="img-fluid" id="psna-logo" src="" alt="PSNA Logo" width="250" height="250">
                </div>
                <div class="text-center mb-4">
                    <h1 class="h4 fw-bold text-primary">Welcome</h1>
                    <p class="text-muted">Log in to access your account</p>
                </div>

                <? if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        Invalid Creditionals
                    </div>
                <? } ?>

                <form method="post" action="login">
                    <div class="form-floating mb-3">
                        <input name="email" type="text" class="form-control" id="floatingInput" placeholder="name@example.com" required>
                        <label for="floatingInput"><i class="bi bi-person me-2"></i>Username</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                        <label for="floatingPassword"><i class="bi bi-lock me-2"></i>Password</label>
                    </div>
                    <button class="btn btn-primary w-100 py-2" type="submit">Log in</button>
                    <p class="text-center text-muted mt-3 mb-0">Facing issues? Contact Tutor
                    </p>
                </form>
            </div>
        </div>
    </main>


    <!-- <footer class="footer mt-auto py-3 position-fixed bottom-0">
        <div class="container">
            <span class="text-body-secondary">Developed by Yuheswari, Aswin & Mentored by Dr.M.Buvana @CSE, PSNA </span>
        </div>
    </footer> -->

<? } ?>