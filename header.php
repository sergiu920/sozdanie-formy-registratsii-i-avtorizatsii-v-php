<?php
    //Запускаем сессию
    session_start();
?>
 
<!DOCTYPE html>
<html>
    <head>
        <title>Название нашего сайта</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    </head>
    <body>
 
        <div id="header">
            <h2>Шапка сайта</h2>
 
            <a href="/index.php">Главная</a>
 
            <div id="auth_block">
            <?php
                //Проверяем авторизован ли пользователь
                if(!isset($_SESSION['email']) && !isset($_SESSION['password'])){
                    // если нет, то выводим блок с ссылками на страницу регистрации и авторизации
            ?>
                    <div id="link_register">
                        <a href="/form_register.php">Регистрация</a>
                    </div>
             
                    <div id="link_auth">
                        <a href="/form_auth.php">Авторизация</a>
                    </div>
            <?php
                }else{
                    //Если пользователь авторизован, то выводим ссылку Выход
            ?> 
                    <div id="link_logout">
                        <a href="/logout.php">Выход</a>
                    </div>
            <?php
                }
            ?>
            </div>
             <div class="clear"></div>
        </div>