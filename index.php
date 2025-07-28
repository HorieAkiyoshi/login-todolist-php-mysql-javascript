<?php
require("config/connection.php");

if(isset($_POST['email']) && isset($_POST['password']) && !empty($_POST['email']) && !empty($_POST['password'])){
    $email = clearPost($_POST['email']);
    $password = clearPost($_POST['password']);
    $cript_password = sha1($password);

    $sql = $pdo->prepare("SELECT * FROM users WHERE email=? AND password=? LIMIT 1");
    $sql->execute(array($email, $cript_password));
    $user = $sql->fetch(PDO::FETCH_ASSOC);

    if($user){
        if($user['status'] == "confirmed"){
            $token = sha1(uniqid().date("Y-m-d-H-i-s"));

            $sql = $pdo->prepare("UPDATE users SET token=? WHERE email=? AND password=?");
            if($sql->execute(array($token, $email, $cript_password))){
                $_SESSION['TOKEN'] = $token;

                header('location: restricted.php');
            }
        }else{
            $loginErr = "登録したメールアドレスで登録の確認をしてください。";
        }
    }else{
            $loginErr = "ユーザー名またはパスワードが正しくありません!";
        }
        
}

?><!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <title>ログイン | Dev Horie</title>
</head>
<body>
    <form method="POST">
        <h1>ログイン</h1>
        <?php if(isset($_GET['result']) && ($_GET['result'] == "ok")){?>
            <div class="success  animate__animated animate__tada" id="success">
                登録が完了しました！
            </div>
        <?php } ?>

        <?php if(isset($loginErr)){?>
            <div class="general-error animate__animated animate__tada">
                <?php echo $loginErr;?>
            </div>
        <?php } ?>

        
        <div class="input-group">
            <img class="input-icon" src="img/user.png" alt="user icon">
            <input type="email" name="email" placeholder="メールアドレスを入力してください" required>
        </div>
        
        <div class="input-group">
            <img class="input-icon" src="img/lock.png" alt="lock icon">
            <input type="password" name="password" placeholder="パスワードを入力してください" required>
        </div>
        
        <a href="forget.php">パスワードをお忘れですか？</a>

        <button class="btn-blue" type="submit">ログイン</button>
        
        <a href="register.php">まだ登録していません</a>
    </form>
        
    <?php if(isset($_GET['result']) && ($_GET['result'] == "ok")){?>
        <script>
            const success = document.getElementById("success");
            setTimeout(() => {
                success.classList.add("hidden");
            }, 3000);
        </script>
    <?php } ?>
</body>
</html>