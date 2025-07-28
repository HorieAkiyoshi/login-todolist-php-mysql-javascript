<?php
require("config/connection.php");

$user = auth($_SESSION['TOKEN']);
if(!$user){
    header('location: index.php');
}

?><!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/todolist.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>やることリスト</title>
</head>
<body>

    <header>
        <div class="logo"><h2>ようこそ！</h2></div>
        <div class="btn"><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> ログアウト</a></div>
    </header>

    <h1>やることリスト</h1>

    <div class="container">
        <input id="inputTask" type="text" placeholder="やることを追加">
        <button id="btn-add" onclick="addtask()" class="btn-add">追加</button>
    </div>

    <main id="main"></main>

    <script src="js/index.js"></script>
</body>
</html>