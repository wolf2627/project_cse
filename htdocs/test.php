<pre>
<?php

include 'libs/load.php';

// $admin = new Role();

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

$faculty = new Faculty();

$result = $faculty->getTestId("Serial Test 1", "2022-2026", "5", "GE2C25", "CSE");

if($result) {
    echo "Test ID: " . $result;
} else {
    echo "Test ID not found";
}

echo "<br>";

$result = $faculty->getClassId("2022-2026", "5", "GE2C25", "A", "CSE");

if($result) {
    echo "Class ID: " . $result;
} else {
    echo "Class ID not found";
}


?>
</pre>