<pre>
    <?php
    include_once 'libs/load.php';

    print_r($_POST);

    $permissions = new Permission();

    try{
        if(!isset($_POST['permissions'])){
            $permissionsRec = null;
        } else {
            $permissionsRec = $_POST['permissions'];
        }
        $result = $permissions->assignPermissionToRole($_POST['role'], $permissionsRec);

        print_r($result);

    } catch (Exception $e) {
        echo $e->getMessage();
    }

    



    ?>
</pre>