<pre>
    <?php
    include_once 'libs/load.php';

    print_r($_POST);

    $permissions = new Permission();

    try{
        $result = $permissions->assignPermissionToRole($_POST['role'], $_POST['permissions']);

        echo "Permission assigned to role";

    } catch (Exception $e) {
        echo $e->getMessage();
    }

    



    ?>
</pre>