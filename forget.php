<?php
require("config/connection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config/PHPMailer/src/Exception.php';
require 'config/PHPMailer/src/PHPMailer.php';
require 'config/PHPMailer/src/SMTP.php';

if(isset($_POST['email']) && !empty($_POST['email'])){
    $email = clearPost($_POST['email']);
    $status = "confirmed";
    
    $sql = $pdo->prepare("SELECT * FROM users WHERE email=? AND status=? LIMIT 1");
    $sql->execute(array($email,$status));
    $user = $sql->fetch(PDO::FETCH_ASSOC);

    $mail = new PHPMailer(true);
    $code = sha1(uniqid());

    if($user){
        

        $sql = $pdo->prepare("UPDATE users SET recover_password=? WHERE email=?");
        if($sql->execute(array($code, $email))){
            

            try{
                $mail->setFrom('h.a.horie@gmail.com', 'ログインシステム');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);                                  
                $mail->Subject = ' 新しいパスワードを入力してください。';
                $mail->Body    = '<h1>パスワード再設定 :</h1> <br><br><a style="background:green; color:white; text-decoration:none; padding:20px; border-radius:5px;" href="https://devhorie.com/recover-password.php?code='.$code.'">パスワード再設定</a>';

                $mail->send();
                header('location: sending-email-recover.php');

            }catch (Exception $e) {
                echo "確認メールの送信中に問題が発生しました : {$mail->ErrorInfo}";
            }
        }

    }else{
        $userErr = "メールアドレスの取得に失敗しました。";
    }
}

?><!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <title>パスワードを忘れた | Dev Horie</title>
</head>
<body>
    <form method="POST">
        <h1>パスワードの再発行</h1>

        <?php if(isset($userErr)){?>
            <div class="general-error animate__animated animate__tada">
                <?php echo $userErr;?>
            </div>
        <?php } ?>

        <p>システムに登録したメールアドレスを入力してください。</p>
        
        <div class="input-group">
            <img class="input-icon" src="img/user.png" alt="user icon">
            <input type="email" name="email" placeholder="メールアドレスを入力してください" required>
        </div>

        <button class="btn-blue" type="submit">パスワードを再設定</button>
        
        <a href="index.php">ログインに戻る</a>
    </form>
        
</body>
</html>