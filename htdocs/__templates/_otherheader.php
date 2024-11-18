<div class="container">
    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <svg class="bi me-2" width="40" height="32">
                <use xlink:href="#bootstrap"></use>
            </svg>
            <span class="fs-4">Department of Computer Science and Engineering</span>
        </a>

        <ul class="nav nav-pills">
            <?
            if (Session::currentScript() == "index") {
            ?>
                <li class="nav-item"><span class="fs-4">Welcome</span></li>
            <?
            } else {
            ?>
                <li class="nav-item"><span class="fs-4"><?= Session::currentScript(); ?></span></li>
            <? } ?>
        </ul>
    </header>
</div>