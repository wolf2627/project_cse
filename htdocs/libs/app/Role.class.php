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
        foreach ($roles as $role) {
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

        $userRoles = $UserRoleCollection->findOne(["user_id" => $userId]);

        if ($userRoles) {
            return $userRoles['roles'];
        }

        return [];
    }

    public function assignOtherRoles($roleCategory, $userId, $rolesId)
    {
        $userRoleCollection = $this->conn->user_roles;
        $rolesCollection = $this->conn->roles;

        //TODO: Check for User Existence

        if (empty($rolesId)) {
            if ($this->unassignAllRoles($userId)) {
                return [];
            }
        }

        //validate the roles
        $validatedRoles = [];
        foreach ($rolesId as $roleId) {
            try {
                $roleObjId = new MongoDB\BSON\ObjectId($roleId);
            } catch (Exception $e) {
                throw new Exception( $roleId . 'id is Invalid');
            }

            if ($rolesCollection->findOne(['_id' => $roleObjId])) {
                $validatedRoles[] = $roleObjId;
            } else {
                throw new Exception($roleId . ' not found');
            }
        }

        //update the roles to the user
        $userRoleCollection->updateOne(
            ["category" => $roleCategory, 'user_id' => $userId],
            ['$set' => ["roles" => $validatedRoles]],
            ['upsert' => true] // Create the document if it doesn't exist
        );


        $updatedUserDoc = $userRoleCollection->findOne(['user_id' => $userId]);

        $finalRoles = isset($updatedUserDoc['roles'])
            ? array_map('strval', (array) $updatedUserDoc['roles'])
            : [];

        $finalResult = [];

        foreach ($finalRoles as $role) {
            $roleDoc = $rolesCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($role)]);
            if ($roleDoc) {
                $finalResult[] = [
                    'id' => (string) $roleDoc['_id'],
                    'name' => $roleDoc['role_name'],
                    'category' => $roleDoc['role_category'],
                    'description' => $roleDoc['description']
                ];
            }
        }

        return $finalResult;
    }


    public function unassignAllRoles($userId)
    {
        $userRoleCollection = $this->conn->user_roles;

        if (!$userRoleCollection->findOne(['user_id' => $userId])) {
            //throw new Exception('No roles assigned to the user');
            return true;
        }

        $result = $userRoleCollection->deleteOne(['user_id' => $userId]);

        if ($result->getDeletedCount() === 0) {
            throw new Exception('Failed to unassign roles');
        }

        return true;
    }
}
