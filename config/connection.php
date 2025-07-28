<?php
session_start();

$mode = 'production';   


if($mode == 'local'){
    $server = 'localhost';
    $user = 'root';
    $password = '';
    $bank = "php-login";
}

if($mode == 'production'){
    $server = 'localhost';
    $user = 'u338011619_logitest';
    $password = 'Meuprimeirosite1';
    $bank = "u338011619_loginexample";
}

try{
    $pdo = new PDO("mysql:host=$server;dbname=$bank",$user,$password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch(PDOException $error){
    echo "Failed to connect to the bank!";
}

function clearPost($date){
    $date = trim($date);
    $date = stripslashes($date);
    $date = htmlspecialchars($date);
    return $date;
}

function auth($tokenSession){
    global $pdo;
    $sql = $pdo->prepare("SELECT * FROM users WHERE token=? LIMIT 1");
    $sql->execute(array($tokenSession));
    $userLogin = $sql->fetch(PDO::FETCH_ASSOC);

    if(!$userLogin){
        return false;
    }else{
        return $userLogin;
    }
}



?>