<?php
 // CONFIG FILE
 include $_SERVER['DOCUMENT_ROOT'] . '/AvailabilityWeb/config/config.php';

 //DEBUG
 /*
 $_POST['id']=3;
 $_POST['action']='delete';
*/

if (isset($_POST['id'])) {

    $id = $_POST['id'];

    if (isset($_POST['action']) && $_POST['action'] == 'delete') {


        $deleteQuery = "DELETE FROM `AvailabilityWeb`.`t_dependencies_logs` WHERE `id_dependencies` = ?;";
        $deleteResult = MYSQLI___request($MYSQLI_connection, $deleteQuery, 'DELETE', [$id]);

        $deleteQuery = "DELETE FROM `AvailabilityWeb`.`t_dependencies` WHERE `id` = ?;";
        $deleteResult = MYSQLI___request($MYSQLI_connection, $deleteQuery, 'DELETE', [$id]);
        
        if ($deleteResult['ret_code'] == 0) { 

            $_SESSION['infoMessage'] = "<i class='fas fa-info-circle'></i> Item deleted successfully !";
			$_SESSION['infoMessageType'] = "warning"; 

        } else {
            $_SESSION['infoMessage'] = "Deletion failed";
			$_SESSION['infoMessageType'] = "danger"; 
        }
    }

   
    if (isset($_POST['action']) && $_POST['action'] == 'update') {

        if (isset($_POST['id'], $_POST['name'], $_POST['url'], $_POST['description'], $_POST['group'], $_POST['proxy'])) {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $url = $_POST['url'];
            $description = $_POST['description'];
            $group = $_POST['group'];
            $proxy = $_POST['proxy'];
    
            $updateQuery = "UPDATE `AvailabilityWeb`.`t_dependencies` 
                            SET `name` = ?, `url` = ?, `description` = ?, `group` = ?, `proxy` = ? 
                            WHERE `id` = ?;";
    
            $updateResult = MYSQLI___request($MYSQLI_connection, $updateQuery, 'UPDATE', [$name, $url, $description, $group, $proxy, $id]);
            
            if ($updateResult['ret_code'] == 0) {
                $_SESSION['infoMessage'] = "<i class='fas fa-info-circle'></i> Item updated successfully!";
                $_SESSION['infoMessageType'] = "success";
            } else {
                $_SESSION['infoMessage'] = "Update failed";
                $_SESSION['infoMessageType'] = "danger";
            }
        } else {
            $_SESSION['infoMessage'] = "Missing fields for update";
            $_SESSION['infoMessageType'] = "danger";
        }
    }
    
} 

