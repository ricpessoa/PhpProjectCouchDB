<?php

/**
 * Login User by username and password
 * 
 */
    $username = $_POST["username"];
    $password = $_POST["password"];
    $response = array();

    if ($username == "rpessoa" && $password == "rpessoa") {
        $response['error'] = false;
        $response['message'] = "Login Successfull";
    } else {
        $response['error'] = true;
        $response['message'] = "An error occurred. Please try again";
    }
    // Store user details in db
//    include_once './db_functions.php';
//
//    $db = new DB_Functions();
//    $res = $db->getUserByUsernamePassword($name, $pass);
//while ($row = mysql_fetch_array($res)) {
// //`username` `password` `gcm_regid`
//$phpConfirm = $row["id"].";".$row["username"].";".$row["password"].";".$row["gcm_regid"];
//}
    //echo $response;
        $response[test] = "username=" . $username . "password=" . $password;

    echo json_encode($response);

?>


