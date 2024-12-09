<?php

${basename(__FILE__, '.php')} = function () {

    if (!Session::isAuthenticated()) {
        $this->response($this->json(['message' => 'Unauthorized']), 401);
    }
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
?>
            <!-- <input type="hidden" id="roleCategory" value="student"> -->
            <!-- <div class="mb-3">
                <label for="roleCategory" class="form-label">Disabled input</label>
                <input type="text" id="roleCategory" value="<?=$category?>" class="form-control" placeholder="<?=$category?>">
            </div> -->
            <div class="mb-3">
                <label for="ReferenceNumber" class="form-label">Reference Number</label>
                <input type="text" class="form-control" id="ReferenceNumber" placeholder="Reference Number" aria-describedby="ReferenceNumberHelp">
                <div id="ReferenceNumberHelp" class="form-text">For Faculty Reference Number is Faculty Id, and For Student Reference Number is Register Number.</div>
            </div>
            <div class="mb-3">
                <label for="roleId" class="form-label">Select Role:</label>
                <select id="roleId" class="form-control" required>
                    <option value="">Select Role</option>
                    <?php
                    $roleCategory = $_GET['category'];
                    $role = new Role();
                    $roles = $role->getRoles($roleCategory);
                    foreach ($roles as $role) {
                    ?>
                        <option value="<?php echo $role['_id']; ?>"><?php echo $role['role_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="button" class="btn btn-primary" onclick="manageRoleSubmitForm('')">Assign</button>
<? } else {
        $this->response($this->json(['message' => 'bad request']), 400);
    }
};
?>