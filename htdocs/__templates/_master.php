<!doctype html>
<html lang="en" data-bs-theme="auto">

<?
Session::loadTemplate('_head'); // load head 
?>

<body>

    <? Session::loadTemplate('svg'); // load svg 
    ?>

    <?
    if (Session::currentScript() == 'login' || Session::currentScript() == 'index' || Session::$isError) {
        Session::loadTemplate('_otherheader'); // load signin header
        Session::loadTemplate(Session::currentScript());
    } else { ?>
        <div class="sidebar-overlay"></div>

        <? Session::loadTemplate('_sidebar'); ?>

        <div class="main-content d-flex flex-column">

            <? Session::loadTemplate('_header') ?>

            <main class="flex-grow-1 px-4" style="padding-bottom: 60px;">
                <? Session::loadTemplate(Session::currentScript()); ?>
            </main>

            <? Session::loadTemplate('_footer'); // load footer
            ?>
        </div>

    <? } ?>



    <? Session::loadTemplate('_modal'); //load modal template 
    ?>

    <script src="<?= get_config("base_path"); ?>assets/dist/js/bootstrap.bundle.min.js"></script>


    <!-- JS jas to be loaded in footer, Not in Header as it impacts page load time. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.js" integrity="sha384-eI7PSr3L1XLISH8JdDII5YN/njoSsxfbrkCTnJrzXt+ENP5MOVBxD+l6sEG4zoLp" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>

    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Include jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


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