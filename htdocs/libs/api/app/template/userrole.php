<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['category', 'user_id'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $category = $this->_request['category'];
        $user_id = $this->_request['user_id'];

        $role = new Role();
        $allRoles = $role->getRoles($category); // Retain full role details

        // Convert assigned roles to strings for comparison
        $assignedRoles = $role->getAssignedRoles($user_id);
        if (empty($assignedRoles)) {
            $userRoles = [];
        } else {
            $assignedRoles = $assignedRoles->getArrayCopy();
            $userRoles = array_map(fn($roleId) => (string) $roleId, $assignedRoles);
        }

        ?>
        <div class="container">
            <h4>Roles</h4>
            <?php foreach ($allRoles as $role) : 
                // Extract role ID for comparison
                $roleId = (string) $role['_id'];
                $isChecked = in_array($roleId, $userRoles) ? 'checked' : '';
            ?>
            <div class="form-check">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="role[]" 
                    value="<?= $roleId ?>" 
                    <?= $isChecked ?> 
                    id="role_<?= $roleId ?>" 
                    data-toggle="tooltip" 
                    title="<?= htmlspecialchars($role['description'], ENT_QUOTES, 'UTF-8') ?>"
                >
                <label class="form-check-label" for="role_<?= $roleId ?>">
                    <?= htmlspecialchars($role['role_name'], ENT_QUOTES, 'UTF-8') ?>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    } else {
        $this->response($this->json(['message' => 'Bad Request']), 400);
    }
};
