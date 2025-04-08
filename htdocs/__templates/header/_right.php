<div class="d-flex align-items-center px-4">
    <!-- Theme Toggle -->
    <div class="dropdown bd-mode-toggle rounded-circle me-2">
        <button class="btn btn-bd-primary py-2 d-flex align-items-center no-border" style="border: none;" id="bd-theme" type="button"
            aria-expanded="false" data-bs-toggle="dropdown" aria-label="Toggle theme (auto)" width="32" height="32">
            <svg class="bi my-1 theme-icon-active" width="1em" height="1em">
                <use href="#circle-half"></use>
            </svg>
            <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme">
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light"
                    aria-pressed="false">
                    <svg class="bi me-2 opacity-50" width="1em" height="1em">
                        <use href="#sun-fill"></use>
                    </svg>
                    Light
                    <svg class="bi ms-auto d-none" width="1em" height="1em">
                        <use href="#check2"></use>
                    </svg>
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark"
                    aria-pressed="false">
                    <svg class="bi me-2 opacity-50" width="1em" height="1em">
                        <use href="#moon-stars-fill"></use>
                    </svg>
                    Dark
                    <svg class="bi ms-auto d-none" width="1em" height="1em">
                        <use href="#check2"></use>
                    </svg>
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto"
                    aria-pressed="true">
                    <svg class="bi me-2 opacity-50" width="1em" height="1em">
                        <use href="#circle-half"></use>
                    </svg>
                    Auto
                    <svg class="bi ms-auto d-none" width="1em" height="1em">
                        <use href="#check2"></use>
                    </svg>
                </button>
            </li>
        </ul>
    </div>

    <!-- <div class="dropdown text-end">
        <a href="#" class="d-block link-body-emphasis text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
        </a>
        <ul class="dropdown-menu text-small">
            <li><a class="dropdown-item" href="#">My Account</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <?php 
            if(Session::isAuthenticated()){
                ?>
                    <li><a class="dropdown-item" href="/" id="signOutBtn">Sign in</a></li>
                <?
            } else {
                ?>
                    <li><a class="dropdown-item" href="?logout" id="signOutBtn">Sign out</a></li>
                <?
            }
            ?>
    
        </ul>
    </div> -->


    <!-- <div>
                <a href="?logout" class="btn btn-bd-primary no-border" id="signOutBtn" style="border: none;">
                    <svg class="bi d-block me-2" width="16" height="16">
                        <use xlink:href="#door-open-fill"></use>
                    </svg>
                </a>
            </div> -->

</div>