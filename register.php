<?php
require('config/connection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'config/PHPMailer/src/Exception.php';
require 'config/PHPMailer/src/PHPMailer.php';
require 'config/PHPMailer/src/SMTP.php';

if(isset($_POST["name"]) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['recover_password'])){
    if(empty($_POST['name']) or empty($_POST['email']) or empty($_POST['password']) or empty($_POST['recover_password']) or empty($_POST['terms'])){
        $general_error = "すべての項目は必須入力です。";
    }else{
        $name = clearPost($_POST['name']);
        $email = clearPost($_POST['email']);
        $password = clearPost($_POST['password']);
        $cript_password = sha1($password);
        $recover_password = clearPost($_POST['recover_password']);
        $terms = clearPost($_POST['terms']);

        if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
            $nameErr = "使用できるのは文字とスペースのみです。";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "無効なメールアドレスです。";
        }

        if(strlen($password) <= 6){
            $passwordErr = "パスワードは6文字以上で入力してください。";
        }

        if($recover_password !== $password){
            $recover_passwordErr = "パスワードと確認用パスワードが一致しません。";
        }

        if($terms !== "ok"){
            $checkboxErr = "disabled";
        }

        if(!isset($general_error) && !isset($nameErr) && !isset($emailErr) && !isset($passwordErr) && !isset($recover_passwordErr) && !isset($checkboxErr)){
            $sql = $pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
            $sql->execute(array($email));
            $user = $sql->fetch();

            if(!$user){
                $recover_password = "";
                $token = "";
                $status = "new";
                $date_register = date("Y-m-d");
                $code_confirm = uniqid();
                $sql = $pdo->prepare("INSERT INTO users VALUES (null,?,?,?,?,?,?,?,?)");
                if($sql->execute(array($name, $email, $cript_password, $recover_password, $token, $status,$date_register,$code_confirm))){

                    if($mode == 'local'){
                        header('location: index.php?result=ok');
                    }

                    if($mode == 'production'){
                        $mail = new PHPMailer(true);

                        try{
                            $mail->setFrom('h.a.horie@gmail.com', 'ログインシステム');
                            $mail->addAddress($email, $name);

                            $mail->isHTML(true);                                  
                            $mail->Subject = 'ご登録の確認をお願いいたします。';
                            $mail->Body    = '<h1>下記のメールアドレスをご確認ください :</h1> <br><br><a style="background:green; color:white; text-decoration:none; padding:20px; border-radius:5px;" href="https://devhorie.com/confirm.php?code_confirm='.$code_confirm.'">メールを確認</a>';

                            $mail->send();
                            header('location: thanks.php');

                        }catch (Exception $e) {
                            echo "確認メールの送信中に問題が発生しました : {$mail->ErrorInfo}";
                        }
                    }
                    
                }

            }else{
                $general_error = "このユーザーは既に登録されています。";
            }
        }
    }
}
?><!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


    <title>新規登録 | Dev Horie</title>
</head>
<body>
    <form action="" method="POST">
        <h1>新規登録</h1>

        <?php if(isset($general_error)){?>
            <div class="general-error animate__animated animate__tada">
                <?php echo $general_error;?>
            </div>
        <?php } ?>

        <div class="input-group">
            <img class="input-icon" src="img/card.png" alt="card icon">
            <input <?php if(isset($general_error) or isset($nameErr)){ echo 'class="error-input"'; } ?>  name="name" type="text" placeholder="フルネーム" <?php if(isset($_POST['name'])){ echo "value='".$_POST['name']."'" ;} ?> required>
            <?php if(isset($nameErr)){?>
            <div class="error"><?php echo $nameErr ?></div>
            <?php } ?>
        </div>
        
        <div class="input-group">
            <img class="input-icon" src="img/user.png" alt="user icon" >
            <input <?php if(isset($general_error) or isset($emailErr)){ echo 'class="error-input"'; } ?> type="email"  name="email" placeholder="メール" <?php if(isset($_POST['email'])){ echo "value='".$_POST['email']."'" ;} ?> required>
            <?php if(isset($emailErr)){?>
                <div class="error"><?php echo $emailErr ?></div>
            <?php } ?>
        </div>

        <div class="input-group">
            <img class="input-icon" src="img/lock.png" alt="lock icon" >
            <input <?php if(isset($general_error) or isset($passwordErr)){ echo 'class="error-input"'; } ?> type="password" name="password" placeholder="パスワードは6文字以上で入力してください" <?php if(isset($_POST['password'])){ echo "value='".$_POST['password']."'" ;} ?> required>
            <?php if(isset($passwordErr)){?>
                <div class="error"><?php echo $passwordErr ?></div>
            <?php } ?>
        </div>
        
        <div class="input-group">
            <img class="input-icon" src="img/lock.png" alt="lock icon" >
            <input <?php if(isset($general_error) or isset($recover_passwordErr)){ echo 'class="error-input"'; } ?> type="password" name="recover_password" placeholder="パスワード再入力" <?php if(isset($_POST['recover_password'])){ echo "value='".$_POST['recover_password']."'" ;} ?>required>
            <?php if(isset($recover_passwordErr)){?>
                <div class="error"><?php echo $recover_passwordErr ?></div>
            <?php } ?>
        </div>

        <div style="display:flex;justify-content:center;align-items:center;width:100%;margin-left:5%;" <?php if(isset($general_error) or isset($checkboxErr)){ echo 'class="error-input input-group"'; }else{ echo 'class="input-group"' ;}?> >
            <div style="display:flex;justify-content:center;align-items:center;width:15%;">
                <input style="display:block; width:80%;" type="checkbox" id="terms" name="terms" value="ok" required>
            </div>
            <div style="display:block; width:80%;" style="display:flex;justify-content:center;align-items:center;width:85%;">
                <label for="terms">登録することで、<a class="link" href="terms.php">プライバシーポリシーと利用規約</a>に同意したことになります。</label>
            </div>
        </div>
        
        <button class="btn-blue" type="submit">登録</button>
        <a href="index.php">すでに登録済みです</a>
    </form>
</body>
</html>