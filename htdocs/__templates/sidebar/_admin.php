<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-body-secondary text-uppercase">
    <span>Admin</span>
</h6>

<li class="nav-item">
    <button class="nav-link d-flex align-items-center gap-2 btn-toggle" data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="false">
        <svg class="bi">
            <use xlink:href="#new-folder"></use>
        </svg>
        Create
        <svg class="bi arrow-mov ">
            <use xlink:href="#plus"></use>
        </svg>
    </button>
    <div class="collapse" id="dashboard-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" href="/createuser">
                    <svg class="bi">
                        <use xlink:href="#person-fill" />
                    </svg>
                    User
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" href="/createsubject">
                    <svg class="bi">
                        <use xlink:href="#journal-plus" />
                    </svg>
                    Subject
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" href="/createtest">
                    <svg class="bi">
                        <use xlink:href="#journal-plus" />
                    </svg>
                    Test
                </a>
            </li>
        </ul>
    </div>
</li>

<li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2" href="/enrollsubjects">
        <svg class="bi">
            <use xlink:href="#file-person-fill" />
        </svg>
        Enroll Students
    </a>
</li>

<li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2" href="/assignfaculty">
        <svg class="bi">
            <use xlink:href="#bi-person-rolodex" />
        </svg>
        Assign Faculty
    </a>
</li>

<li class="nav-item">
    <button class="nav-link d-flex align-items-center gap-2 btn-toggle" data-bs-toggle="collapse" data-bs-target="#Role-collapse" aria-expanded="false">
        <svg class="bi">
            <use xlink:href="#new-folder"></use>
        </svg>
        Role
        <svg class="bi arrow-mov ">
            <use xlink:href="#plus"></use>
        </svg>
    </button>
    <div class="collapse" id="Role-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" href="/assignrole">
                    <svg class="bi">
                        <use xlink:href="#file-earmark-text" />
                    </svg>
                    Assign
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" href="/managerole">
                    <svg class="bi">
                        <use xlink:href="#file-earmark-text" />
                    </svg>
                    Manage
                </a>
            </li>
        </ul>
    </div>
</li>


<li class="nav-item">
    <button class="nav-link d-flex align-items-center gap-2 btn-toggle" data-bs-toggle="collapse" data-bs-target="#Permission-collapse" aria-expanded="false">
        <svg class="bi">
            <use xlink:href="#new-folder"></use>
        </svg>
        Permission
        <svg class="bi arrow-mov ">
            <use xlink:href="#plus"></use>
        </svg>
    </button>
    <div class="collapse" id="Permission-collapse">
        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center gap-2" href="/managepermission">
                    <svg class="bi">
                        <use xlink:href="#file-earmark-text" />
                    </svg>
                    Manage
                </a>
            </li>
        </ul>
    </div>
</li>

<li class="nav-item">
    <a class="nav-link d-flex align-items-center gap-2" href="/role-permission-manage">
        <svg class="bi">
            <use xlink:href="#file-earmark-text" />
        </svg>
        Grant Permission
    </a>
</li>