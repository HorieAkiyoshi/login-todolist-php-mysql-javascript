<?php
require('config/connection.php');

if(isset($_GET['code_confirm']) && !empty($_GET['code_confirm'])){

    $code = clearPost($_GET['code_confirm']);

    $sql = $pdo->prepare("SELECT * FROM users WHERE confirm_code=? LIMIT 1");
    $sql->execute(array($code));
    $user = $sql->fetch(PDO::FETCH_ASSOC);
    if(isset($user)){
        $status = "confirmed";

        $sql = $pdo->prepare("UPDATE users SET status=? WHERE confirm_code=?");
        if($sql->execute(array($status, $code))){
            
            header('location: index.php?result=ok');
        }
    }else{
        echo "<h1>確認コードが無効です。</h1>";
    }
}
?>