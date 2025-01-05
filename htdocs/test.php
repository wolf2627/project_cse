<pre>

<?php

include 'libs/load.php';

// $role = new Role();

// $category = 'faculty';
// $user_id = '1011';

// $roles = $role->getRoles('faculty');

// print_r($roles);

// $assignedRoles = $role->getAssignedRoles('1011');

// print_r($assignedRoles);

// $allRoles = $role->getRoles($category);

// print_r($allRoles);

// // Convert assigned roles to strings for comparison
// $userRoles = array_map(fn($roleId) => (string) $roleId, $role->getAssignedRoles($user_id)->getArrayCopy());

// print_r($userRoles);

// foreach ($allRoles as $role) {
//     // Extract role ID for comparison
//     $roleId = (string) $role['_id'];
//     $isChecked = in_array($roleId, $userRoles) ? 'checked' : 'Not Checked';
//     echo $roleId . ' ' . $isChecked . '<br>';
// }

// try {
//     $assginRole = $role->assignOtherRoles('faculty', '1011', ['675d28054fe53545bb047f02', '675d28054fe53545bb047f03']);
//     print_r($assginRole);
// } catch (Exception $e) {
//     echo $e->getMessage();
// }


$result = [""];

if (empty($result)) {
    echo 'Empty';
    if(is_array($result)) {
        echo 'Array';
    }
} else {
    echo 'Not Empty';
}

$result = ['a', 'b', 'c'];

if (empty($result)) {
    echo 'Empty';

    if(is_array($result)) {
        echo 'Array';
    }

} else {
    echo 'Not Empty';
}



?>


</pre>