<?php
$user_class->permission_end($module, 'admin');
if(isset($_POST['add_module']) or isset($_POST['add_module_name'])){
        if($_POST['module_name'] !== ''){
                $query->insert_sql("gov_modules", "parent_id, module", "'".$_POST['in_module']."', '".$_POST['add_module_name']."'");
                header("Location: admin.php?module=".$_GET['module']."");
                exit;
        }
        header("Location: admin.php?module=".$_GET['module']."");
        exit;
}

if(isset($_POST['edit_module']) or isset($_POST['edit_module_name'])){
        if($_POST['module_name'] !== ''){
                $query->update_sql("gov_modules", "module = '".$_POST['edit_module_name']."'", "id = '".$_GET['id']."'");
                header("Location: admin.php?module=".$_GET['module']."");
                exit;
        }
        header("Location: admin.php?module=".$_GET['module']."");
        exit;
}

if($_GET['action'] == 'delete'){
        $query->delete_sql("gov_modules", "id = '".$_GET['id']."' OR parent_id = '".$_GET['id']."'");
        header("Location: admin.php?module=".$_GET['module']."");
        exit;
}

echo " <center> ";
echo "<a href=\"admin.php\"><b>"._ADMINISTRATION."</b></a><br /><br />";
echo "<a href=\"admin.php?module=modules\"><b>"._MODULES."</b></a><br /><br />";
load_page($_GET['page'], $pages);

?>

