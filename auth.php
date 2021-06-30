<?php
require("config.php");
include("headers.php");

if($_SERVER['REQUEST_METHOD'] == "POST") :
    $sql = sprintf("SELECT * FROM users WHERE login = '%s' AND password = '%s'",
        $_POST['login'],
        addslashes($_POST['password'])
);
    $rq = $connect->query($sql);
    echo $connect->error;
    if($rq->num_rows > 0):
        //création de la session
        $_SESSION['token'] = md5(date("DdMYHis"));
        $_SESSION['expiration'] = time() + 1 * 60;
        $response['token'] = $_SESSION['token'];
    else:
        //erruer de login
        $response['error'] = "erreur de login/password";
    endif;
    echo json_encode($response);
else :
    //deconnexion
    unset($_SESSION['token']);
    unset($_SESSION['expiration']);
    $response['message'] = "déconnexion";
    echo json_encode($response);
endif;