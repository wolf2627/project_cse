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
    // echo $email;
    $result = UserSession::authenticate($email, $password);
    $login_page = false;
}

if (!$login_page) {
    if ($result) {
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
    ?>
        <script>
            window.location.href = "/login?error=1";
        </script>
    <?
    }
} else {

    ?>
    <main class="form-signin w-100 m-auto">
        <div class="signup-box">
            <form method="post" action="login">
                <img class="mb-4 img-fluid" id="psna-logo" src="" alt="" width="" height="">
                <h1 class="h3 mb-3 fw-normal">Welcome, Log in</h1>

                <? if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        Invalid Creditionals
                    </div>
                <? } ?>

                <div class="form-floating">
                    <input name="email" type="text" class="form-control" id="floatingInput" placeholder="name@example.com">
                    <label for="floatingInput">Username</label>
                </div>
                <div class="form-floating">
                    <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Password</label>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Log in</button>
                <!-- <p class="mt-5 mb-3 text-body-secondary">&copy; 2017–2024</p> -->
            </form>
        </div>
    </main>

    <!-- <footer class="footer mt-auto py-3 position-fixed bottom-0">
        <div class="container">
            <span class="text-body-secondary">Developed by Yuheswari, Aswin & Mentored by Dr.M.Buvana @CSE, PSNA </span>
        </div>
    </footer> -->

<? } ?>