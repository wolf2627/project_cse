<?php

class Role
{
    private $conn = null;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function createRole($roleName, $roleCategory, $description)
    {
        $rolesCollection = $this->conn->roles;

        // Capitalize the first letter of the role name
        $roleName = ucfirst($roleName);

        // Check if the role already exists
        $existingRole = $rolesCollection->findOne(["role_name" => $roleName]);

        if ($existingRole) {
            error_log("Role creation failed: Role '$roleName' already exists.");
            throw new Exception("Role already exists");
        }

        // Proceed with creating the new role
        $result = $rolesCollection->insertOne([
            "role_name" => $roleName,
            "role_category" => $roleCategory,
            "description" => $description
        ]);

        if ($result->getInsertedId()) {
            return $result->getInsertedId();
        } else {
            throw new Exception("Failed to create role");
        }
    }



    public function getRoleId($roleName)
    {

        $rolesCollection = $this->conn->roles;

        $role = $rolesCollection->findOne(["role_name" => $roleName]);

        if ($role) {
            return $role['_id'];
        }

        return null;
    }


    public function updateRole($roleId, $roleName, $description)
    {

        $rolesCollection = $this->conn->roles;

        $result = $rolesCollection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($roleId)],
            ['$set' => ["role_name" => $roleName, "description" => $description]]
        );

        return $result->getModifiedCount();
    }

    public function deleteRole($roleId)
    {

        $rolesCollection = $this->conn->roles;

        $result = $rolesCollection->deleteOne(["_id" => new MongoDB\BSON\ObjectId($roleId)]);

        return $result->getDeletedCount();
    }

    public function getRoles($roleCategory = null)
    {

        $rolesCollection = $this->conn->roles;

        if ($roleCategory) {
            $roles = $rolesCollection->find(["role_category" => $roleCategory]);
        } else {
            $roles = $rolesCollection->find();
        }

        $rolesArray = [];
        foreach ($roles as $role){
            $rolesArray[] = $role;
        }

        return $rolesArray;
    }

    public function getRoleName($roleId)
    {
        $rolesCollection = $this->conn->roles;
        $role = $rolesCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($roleId)]);
        return $role['role_name'];
    }

    public function getAssignedRoles($userId)
    {
        $UserRoleCollection = $this->conn->user_roles; // Assuming 'users' collection

        $userRoles = $UserRoleCollection->findOne(["_id" => new MongoDB\BSON\ObjectId($userId)]);

        if ($userRoles) {
            return $userRoles['roles'];
        }

        return [];
    }

    public function AssignOtherRoles($userId, $roleId) {
        $UserRoleCollection = $this->conn->user_roles; // Assuming 'users' collection
    
        // Check if the role already exists in the user's roles array
        $existingUser = $UserRoleCollection->findOne([
            "_id" => new MongoDB\BSON\ObjectId($userId),
            "roles" => new MongoDB\BSON\ObjectId($roleId)
        ]);
    
        if ($existingUser) {
            error_log("Role update failed: Role with ID '$roleId' already exists for user '$userId'.");
            throw new Exception("Role already exists for the user");
        }
    
        // Update the roles array by adding the new role
        $updateResult = $UserRoleCollection->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($userId)],
            ['$addToSet' => ["roles" => new MongoDB\BSON\ObjectId($roleId)]]
        );
    
        if ($updateResult->getModifiedCount() > 0) {
            return "Role successfully added to the user.";
        } else {
            throw new Exception("Failed to add the role to the user.");
        }
    }
}
