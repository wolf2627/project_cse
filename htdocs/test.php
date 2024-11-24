<pre>
<?php

include 'libs/load.php';

$admin = new Role();

// // Create a Role
// $roleId = $admin->createRole("Professor In Charge", "Manage faculty and student information for the assigned batch in the Department");
// echo "Created Role ID: " . $roleId;

// // Create a Permission
// $permissionId = $admin->createPermission("Generate Report", "Generate report for the assigned batch in the Department");
// echo "Created Permission ID: " . $permissionId;

// // Assign Permission to Role
// $admin->assignPermissionsToRole($roleId, [$permissionId]);
// echo "Assigned Permissions to Role";

// // Assign Role to User
// $admin->assignRolesToUser("6731234567abc", [$roleId]);
// echo "Assigned Role to User";

// // Get Roles for User
// print_r($admin->getRolesForUser("6731234567abc"));

// // Get Permissions for Role
// print_r($admin->getPermissionsForRole($roleId));

// print_r($admin->getRoleId("Professor In Charge"));

print_r($admin->getRoles());

?>
</pre>