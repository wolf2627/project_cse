<!doctype html>
<html lang="en" data-bs-theme="auto">

<? Session::loadTemplate('_head'); // load head 
?>

<body class="d-flex flex-column min-vh-100">

    <? Session::loadTemplate('_modetoggle'); // load mode toggle
    ?>

    <?
    //load svg for using icons 
    Session::loadTemplate('svg'); // load svg
    ?>

    <?
    if (Session::currentScript() == 'login' || Session::currentScript() == 'index' || Session::$isError) {
        Session::loadTemplate('_otherheader'); // load signin header
    } else {
        Session::loadTemplate('_mainheader'); // load header
    }
    ?>

    <div class="container-fluid">
        <div class="row main-row">
            <?
            // load script dynamically based on the current script
            if (Session::$isError) {
                Session::loadTemplate('_error');
            } else {
                if (Session::currentScript() != 'login' && Session::currentScript() != 'index') {
                    Session::loadTemplate('_sidebar'); // load sidebar 
                }
                Session::loadTemplate(Session::currentScript());
            }
            ?>
        </div>
    </div>

    <? Session::loadTemplate('_footer'); // load footer
    ?>

    <? Session::loadTemplate('_modal'); //load modal template 
    ?>

    <script src="<?= get_config("base_path"); ?>assets/dist/js/bootstrap.bundle.min.js"></script>


    <!-- JS jas to be loaded in footer, Not in Header as it impacts page load time. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.js" integrity="sha384-eI7PSr3L1XLISH8JdDII5YN/njoSsxfbrkCTnJrzXt+ENP5MOVBxD+l6sEG4zoLp" crossorigin="anonymous"></script>



    <script src="<?= get_config("base_path") ?>js/app.min.js"></script>
    <script src="<?= get_config("base_path") ?>js/dialog.js"></script>
    <script src="<?= get_config("base_path") ?>js/toast.js"></script>

    <script>
        // This is for security purpose to check if the user is the same user who logged in.
        // Initialize the agent at application startup.
        const fpPromise = import('https://openfpcdn.io/fingerprintjs/v3')
            .then(FingerprintJS => FingerprintJS.load())

        // Get the visitor identifier when you need it.
        fpPromise
            .then(fp => fp.get())
            .then(result => {
                // This is the visitor identifier:
                const visitorId = result.visitorId;
                // console.log(visitorId);
                // $('#fingerprint').val(visitorId);
                // set a cookie
                setCookie('fingerprint', visitorId, 1);
            })
    </script>

</body>

</html>