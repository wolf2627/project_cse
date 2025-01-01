<div class="sidebar-user-image ps-3">
    <div class="d-flex align-items-center text-decoration-none">
        <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
        <div class="d-flex flex-column sidebar-user-info">
            <strong class="text-capitalize"><?=Session::getUser()->getName();?></strong>
            <small class="text-muted text-truncate"><?=$data['role']?></small>
        </div>
    </div>
</div>