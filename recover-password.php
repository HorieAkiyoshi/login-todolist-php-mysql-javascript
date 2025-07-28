<?php
require('config/connection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config/PHPMailer/src/Exception.php';
require 'config/PHPMailer/src/PHPMailer.php';
require 'config/PHPMailer/src/SMTP.php';

if(isset($_GET['code']) && !empty($_GET['code'])){
    $code = clearPost($_GET['code']);

    if(isset($_POST['password']) && isset($_POST['recover_password'])){
        if(empty($_POST['password']) or empty($_POST['recover_password'])){
            $general_error = "すべての項目は必須入力です。";
        }else{
            $password = clearPost($_POST['password']);
            $cript_password = sha1($password);
            $recover_password = clearPost($_POST['recover_password']);

            if(strlen($password) <= 6){
                $passwordErr = "パスワードは6文字以上で入力してください。";
            }

            if($recover_password !== $password){
                $recover_passwordErr = "パスワードと確認用パスワードが一致しません。";
            }

            if(!isset($general_error) && !isset($passwordErr) && !isset($recover_passwordErr)){
                $sql = $pdo->prepare("SELECT * FROM users WHERE recover_password=? LIMIT 1");
                $sql->execute(array($code));
                $user = $sql->fetch();

                if(!$user){
                   echo "無効なパスワード再設定リクエスト。";
                }else{
                    $sql = $pdo->prepare("UPDATE users SET password=? WHERE recover_password=?");
                    if($sql->execute(array($cript_password, $code))){

                        header('location: index.php');
                    }
                }
            }
        }
    }
}else{
    header('location: index.php');
}


?><!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


    <title>パスワード変更 | Dev Horie</title>
</head>
<body>
    <form action="" method="POST">
        <h1>パスワード変更 </h1>

        <?php if(isset($general_error)){?>
            <div class="general-error animate__animated animate__tada">
                <?php echo $general_error;?>
            </div>
        <?php } ?>

        <div class="input-group">
            <img class="input-icon" src="img/lock.png" alt="lock icon" >
            <input <?php if(isset($general_error) or isset($passwordErr)){ echo 'class="error-input"'; } ?> type="password" name="password" placeholder="6文字以上の新しいパスワードを入力してください" <?php if(isset($_POST['password'])){ echo "value='".$_POST['password']."'" ;} ?> required>
            <?php if(isset($passwordErr)){?>
                <div class="error"><?php echo $passwordErr ?></div>
            <?php } ?>
        </div>
        
        <div class="input-group">
            <img class="input-icon" src="img/lock.png" alt="lock icon" >
            <input <?php if(isset($general_error) or isset($recover_passwordErr)){ echo 'class="error-input"'; } ?> type="password" name="recover_password" placeholder="新しいパスワードを再入力してください" <?php if(isset($_POST['recover_password'])){ echo "value='".$_POST['recover_password']."'" ;} ?>required>
            <?php if(isset($recover_passwordErr)){?>
                <div class="error"><?php echo $recover_passwordErr ?></div>
            <?php } ?>
        </div>
      
        <button class="btn-blue" type="submit">パスワード変更</button>

    </form>
</body>
</html>