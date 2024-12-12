<?php

class Permission
{

    private $conn = null;
    private $collection = null;

    public function __construct()
    {
        $this->conn = Database::getConnection();
        $this->collection = $this->conn->permissions;
    }

    // Create a new permission
    public function createPermission($permissionName, $category, $description,)
    {
        $permissionsCollection = $this->collection;

        // Capitalize the first letter of the permission name
        $permissionName = ucfirst($permissionName);

        // Check if the permission already exists
        $existingPermission = $permissionsCollection->findOne(["permission_name" => $permissionName]);

        if ($existingPermission) {
            error_log("Permission creation failed: Permission '$permissionName' already exists.");
            throw new Exception("Permission already exists");
        }

        // Proceed with creating the new permission
        $result = $permissionsCollection->insertOne([
            "permission_name" => $permissionName,
            "permission_category" => $category,
            "description" => $description
        ]);

        if ($result->getInsertedId()) {
            return $result->getInsertedId();
        } else {
            throw new Exception("Failed to create permission");
        }
    }

    // Get the ID of a permission
    public function getPermissionId($permissionName)
    {
        $permissionName = ucfirst($permissionName);

        $permissionsCollection = $this->conn->permissions;

        $permission = $permissionsCollection->findOne(["permission_name" => $permissionName]);

        if ($permission) {
            return $permission['_id'];
        }

        return null;
    }

    // Update a permission
    public function updatePermission($permissionId, $permissionName, $category, $description)
    {
        $permissionsCollection = $this->conn->permissions;

        // Capitalize the first letter of the permission name
        $permissionName = ucfirst($permissionName);

        $result = $permissionsCollection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($permissionId)],
            ['$set' => [
                "permission_name" => $permissionName,
                "permission_category" => $category,
                "description" => $description,
            ]]
        );

        if ($result->getMatchedCount() == 0) {
            error_log("Permission not found with ID: $permissionId");
            throw new Exception("Permission not found");
        }

        if ($result->getModifiedCount()) {
            return true;
        } else {
            // Log a more detailed error message with context
            error_log("No changes were made to the permission: $permissionId");
            throw new Exception("No changes were made to the permission");
        }
    }


    // Delete a permission
    public function deletePermission($permissionId)
    {
        $permissionsCollection = $this->conn->permissions;

        $result = $permissionsCollection->deleteOne(["_id" => new MongoDB\BSON\ObjectId($permissionId)]);

        if ($result->getDeletedCount()) {
            return true;
        } else {
            throw new Exception("Failed to delete permission");
        }
    }

    // Get all permissions
    public function getPermissions($category = null)
    {
        $permissionsCollection = $this->conn->permissions;


        if ($category) {
            $permissions = $permissionsCollection->find(["permission_category" => $category]);
        } else if ($category == null) {
            $permissions = $permissionsCollection->find();
        } else {
            $permissions = [];
        }

        //$permissions = $permissionsCollection->find();

        $permissionsArray = [];

        foreach ($permissions as $permission) {
            $permissionsArray[] = $permission;
        }

        return $permissionsArray;
    }

    // Get a permission by ID
    public function getPermissionByID($permissionId)
    {
        $permissionsCollection = $this->conn->permissions;

        $permission = $permissionsCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($permissionId)]);

        if ($permission) {
            return $permission;
        } else {
            throw new Exception("Permission not found");
        }
    }

    // Get all permissions for a role
    public function getPermissionsForRole($roleId)
    {
        $permissionsCollection = $this->conn->role_permissions;

        $permissions = $permissionsCollection->find(["role_id" => new MongoDB\BSON\ObjectId($roleId)]);

        $permissionsArray = [];

        foreach ($permissions as $permission) {
            $permissionsArray[] = $permission;
        }

        return $permissionsArray;
    }

    // Get permissions by role ID and format the structure
    public function getFormattedPermissionsForRole($roleId)
    {
        $rolePermissionsCollection = $this->conn->role_permissions;

        $rolePermissions = $rolePermissionsCollection->findOne(["role_id" => new MongoDB\BSON\ObjectId($roleId)]);

        if (!$rolePermissions) {
            throw new Exception("Role not found");
        }

        $permissionsArray = [];

        foreach ($rolePermissions['permissions'] as $permissionId) {
            $permission = $this->getPermissionByID($permissionId);
            $permissionsArray[] = [
                'id' => (string) $permission['_id'],
                'name' => $permission['permission_name'],
                'category' => $permission['permission_category'],
                'description' => $permission['description']
            ];
        }

        return $permissionsArray;
    }


    // Get all permissions for a user
    public function getPermissionsForUser($userId)
    {
        $permissionsCollection = $this->conn->permissions;

        $permissions = $permissionsCollection->find();

        $permissionsArray = [];

        foreach ($permissions as $permission) {
            $permissionsArray[] = $permission;
        }

        return $permissionsArray;
    }

    // Check if a user has a permission
    public function userHasPermission($userId, $permissionName)
    {
        $permissionsCollection = $this->conn->permissions;

        $permission = $permissionsCollection->findOne(["permission_name" => $permissionName]);

        if ($permission) {
            return true;
        }

        return false;
    }

    // Check if a role has a permission
    public function roleHasPermission($roleId, $permissionName)
    {
        $permissionsCollection = $this->conn->permissions;

        $permission = $permissionsCollection->findOne(["permission_name" => $permissionName]);

        if ($permission) {
            return true;
        }

        return false;
    }

    // Assign a permission to a role
    public function assignPermissionToRole($roleId, $permissionId)
    {
        $rolesCollection = $this->conn->roles;

        if (!$rolesCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($roleId)])) {
            throw new Exception("Role not found");
        }

        $permissionsCollection = $this->conn->permissions;

        foreach ($permissionId as $id) {
            if (!$permissionsCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($id)])) {
                throw new Exception("Permission not found: $id");
            }
        }

        $rolePermissionsCollection = $this->conn->role_permissions;

        $existingPermissions = $rolePermissionsCollection->findOne(["role_id" => new MongoDB\BSON\ObjectId($roleId)]);

        if ($existingPermissions) {
            echo "Existing permissions found <br>";
            $result = $rolePermissionsCollection->updateOne(
                ["role_id" => new MongoDB\BSON\ObjectId($roleId)],
                ['$addToSet' => ["permissions" => ['$each' => $permissionId]]],
                ['upsert' => true]
            );

            if ($result->getModifiedCount()) {
                return true;
            } else {
                throw new Exception("Failed to update permissions to role <br>");
            }
        } else {

            echo "No existing permissions found <br>";

            $result = $rolePermissionsCollection->insertOne([
                "role_id" => new MongoDB\BSON\ObjectId($roleId),
                "permissions" => $permissionId
            ]);

            if ($result->getInsertedId()) {
                return $result->getInsertedId();
            } else {
                throw new Exception("Failed to assign permission to role");
            }
        }
    }

    // Remove a permission from a role
    public function removePermissionFromRole($roleId, $permissionId)
    {
        $rolesCollection = $this->conn->roles;

        $result = $rolesCollection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($roleId)],
            ['$pull' => ["permissions" => new MongoDB\BSON\ObjectId($permissionId)]]
        );

        if ($result->getModifiedCount()) {
            return true;
        } else {
            throw new Exception("Failed to remove permission from role");
        }
    }

    // Assign a permission to a user
    public function assignPermissionToUser($userId, $permissionId)
    {
        $usersCollection = $this->conn->users;

        $result = $usersCollection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($userId)],
            ['$addToSet' => ["permissions" => new MongoDB\BSON\ObjectId($permissionId)]]
        );

        if ($result->getModifiedCount()) {
            return true;
        } else {
            throw new Exception("Failed to assign permission to user");
        }
    }

    // Remove a permission from a user
    public function removePermissionFromUser($userId, $permissionId)
    {
        $usersCollection = $this->conn->users;

        $result = $usersCollection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($userId)],
            ['$pull' => ["permissions" => new MongoDB\BSON\ObjectId($permissionId)]]
        );

        if ($result->getModifiedCount()) {
            return true;
        } else {
            throw new Exception("Failed to remove permission from user");
        }
    }
}
