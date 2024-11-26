<?php


class Admin
{

    private $conn = null;

    public function __construct(){
        $this->conn = Database::getConnection();
    }

    /**
     * Function to Create Role for the System
     * 
     */
    public function createRole($roleName, $description) {
        
        $rolesCollection = $this->conn->roles;
    
        $result = $rolesCollection->insertOne([
            "role_name" => $roleName,
            "description" => $description
        ]);
    
        return $result->getInsertedId();
    }

    public function getRoleId($roleName) {
        
        $rolesCollection = $this->conn->roles;
    
        $role = $rolesCollection->findOne(["role_name" => $roleName]);
    
        if ($role) {
            return $role['_id'];
        }
    
        return null;
    }
    
    /**
     * Function to Update Roles
     * 
     */

    public function updateRole($roleId, $roleName, $description) {
        
        $rolesCollection = $this->conn->roles;
    
        $result = $rolesCollection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($roleId)],
            ['$set' => ["role_name" => $roleName, "description" => $description]]
        );
    
        return $result->getModifiedCount();
    }

    /**
     * Function to Delete Role
     * 
     */
    public function deleteRole($roleId) {
        
        $rolesCollection = $this->conn->roles;
    
        $result = $rolesCollection->deleteOne(["_id" => new MongoDB\BSON\ObjectId($roleId)]);
    
        return $result->getDeletedCount();
    }

    /**
     * Function to Fetch All Roles
     */

    public function fetchRoles() {
        
        $rolesCollection = $this->conn->roles;
    
        $roles = $rolesCollection->find()->toArray();
    
        $formattedRoles = array_map(function($role) {
            return [
                "_id" => (string) $role["_id"],
                "role_name" => $role["role_name"]
            ];
        }, $roles);
    
        return $formattedRoles;
    }

    //Permission Functions

    /**
     * Function to Create Permission for the System
     * 
     */
    public function createPermission($permissionName, $description) {
        
        $permissionsCollection = $this->conn->permissions;
    
        $result = $permissionsCollection->insertOne([
            "permission_name" => $permissionName,
            "description" => $description
        ]);
    
        return $result->getInsertedId();
    }
    
    /**
     * Function to Update Permission
     * 
     */

    public function updatePermission($permissionId, $permissionName, $description) {
        
        $permissionsCollection = $this->conn->permissions;
    
        $result = $permissionsCollection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($permissionId)],
            ['$set' => ["permission_name" => $permissionName, "description" => $description]]
        );
    
        return $result->getModifiedCount();
    }

    /**
     * Function to Delete Permission
     * 
     */

    public function deletePermission($permissionId) {
        
        $permissionsCollection = $this->conn->permissions;
    
        $result = $permissionsCollection->deleteOne(["_id" => new MongoDB\BSON\ObjectId($permissionId)]);
    
        return $result->getDeletedCount();
    }

    // Role Permission Functions

    /**
     * Function to Assign Permission to Role
     * 
     */
    public function assignPermissionsToRole($roleId, $permissionIds) {
        
        $rolePermissionsCollection = $this->conn->role_permissions;
    
        $result = $rolePermissionsCollection->updateOne(
            ["role_id" => $roleId],
            ['$set' => ["permissions" => $permissionIds]],
            ['upsert' => true]
        );
    
        return $result->getUpsertedId() ?: $result->getModifiedCount();
    }
    
    /**
     * Function to Get Permissions of Role
     * 
     */
    public function getPermissionsForRole($roleId) {
        
        $rolePermissionsCollection = $this->conn->role_permissions;
    
        $mapping = $rolePermissionsCollection->findOne(["role_id" => $roleId]);
    
        if ($mapping) {
            return $mapping['permissions'];
        }
    
        return [];
    }
    
    // User Role Functions

    /**
     * Function to Assign Role to User
     * 
     */
    public function assignRolesToUser($userId, $roleIds) {
        
        $userRolesCollection = $this->conn->user_roles;
    
        $result = $userRolesCollection->updateOne(
            ["user_id" => $userId],
            ['$set' => ["roles" => $roleIds]],
            ['upsert' => true]
        );
    
        return $result->getUpsertedId() ?: $result->getModifiedCount();
    }
    
    /**
     * Function to Get Roles of User
     * 
     */
    public function getRolesForUser($userId) {
        
        $userRolesCollection = $this->conn->user_roles;
    
        $mapping = $userRolesCollection->findOne(["user_id" => $userId]);
    
        if ($mapping) {
            return $mapping['roles'];
        }
    
        return [];
    }
    
    // Fetch All Users With Roles

    /**
     * Function to Get Users by Role
     * 
     */

    public function getUsersByRole($roleId) {
        
        $userRolesCollection = $this->conn->user_roles;
    
        $users = $userRolesCollection->find(["roles" => $roleId]);
    
        $userIds = [];
        foreach ($users as $user) {
            $userIds[] = $user['user_id'];
        }
    
        return $userIds;
    }

    // Fetch All Users With Permissions

    /**
     * Function to Get Users by Permission
     * 
     */
    public function getUsersByPermission($permissionId) {
        
        $rolePermissionsCollection = $this->conn->role_permissions;
        $userRolesCollection = $this->conn->user_roles;
    
        $roles = $rolePermissionsCollection->find(["permissions" => $permissionId]);
    
        $userIds = [];
        foreach ($roles as $role) {
            $users = $userRolesCollection->find(["roles" => $role['role_id']]);
            foreach ($users as $user) {
                $userIds[] = $user['user_id'];
            }
        }
    
        return $userIds;
    }
}
