<?php

//Запускаем сессию
session_start();

//Добавляем файл подключения к БД
require_once("dbconnect.php");

if(isset($_POST["set_new_password"]) && !empty($_POST["set_new_password"])){

    //(1) Место для следующего куска кода

    //Проверяем, если существует переменная token в глобальном массиве POST
    if(isset($_POST['token']) && !empty($_POST['token'])){

        $token = $_POST['token'];
    }else{

        //Останавливаем  скрипт
        exit("<p><strong>Ошибка!</strong> Отсутствует проверочный код ( Передаётся скрытно ).</p>");
    }

    //Проверяем, если существует переменная email в глобальном массиве POST
    if(isset($_POST['email']) && !empty($_POST['email'])){

        $email = $_POST['email'];
    }else{

        //Останавливаем  скрипт
        exit("<p><strong>Ошибка!</strong> Отсутствует адрес электронной почты ( Передаётся скрытно ).</p>");
    }

    if(isset($_POST["password"])){

        //Обрезаем пробелы с начала и с конца строки
        $password = trim($_POST["password"]);

        //Проверяем, совпадают ли пароли
        if(isset($_POST["confirm_password"])){

            //Обрезаем пробелы с начала и с конца строки
            $confirm_password = trim($_POST["confirm_password"]);

            if($confirm_password != $password){
                // Сохраняем в сессию сообщение об ошибке.
                $_SESSION["error_messages"] = "<p class='mesage_error' >Пароли не совпадают</p>";

                //Возвращаем пользователя на страницу установки нового пароля
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."set_new_password.php?email=$email&token=$token");

                //Останавливаем  скрипт
                exit();
            }
        }else{
            // Сохраняем в сессию сообщение об ошибке.
            $_SESSION["error_messages"] = "<p class='mesage_error' >Отсутствует поле для повторения пароля</p>";

            //Возвращаем пользователя на страницу установки нового пароля
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."set_new_password.php?email=$email&token=$token");

            //Останавливаем  скрипт
            exit();
        }

        if(!empty($password)){

            $password = htmlspecialchars($password, ENT_QUOTES);

            //Шифруем папроль
            $password = md5($password."top_secret");

        }else{

            // Сохраняем в сессию сообщение об ошибке.
            $_SESSION["error_messages"] = "<p class='mesage_error' >Пароль не может быть пустым</p>";

            //Возвращаем пользователя на страницу установки нового пароля
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."set_new_password.php?email=$email&token=$token");
            //Останавливаем  скрипт
            exit();
        }

    }else{
        // Сохраняем в сессию сообщение об ошибке.
        $_SESSION["error_messages"] = "<p class='mesage_error' >Отсутствует поле для ввода пароля</p>";

        //Возвращаем пользователя на страницу установки нового пароля
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".$address_site."set_new_password.php?email=$email&token=$token");

        //Останавливаем  скрипт
        exit();
    }

    //(2) Место для следующего куска кода
    $query_update_password = $mysqli->query("UPDATE users SET password='$password' WHERE email='$email'");

    if(!$query_update_password){

        // Сохраняем в сессию сообщение об ошибке.
        $_SESSION["error_messages"] = "<p class='mesage_error' >Возникла ошибка при изменении пароля.</p><p><strong>Описание ошибки</strong>: ".$mysqli->error."</p>";

        //Возвращаем пользователя на страницу установки нового пароля
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".$address_site."set_new_password.php?email=$email&token=$token");

        //Останавливаем  скрипт
        exit();

    }else{
        //Подключение шапки
        require_once("header.php");

        //Выводим сообщение о том, что пароль установлен успешно.
        echo '<h1 class="success_message text_center">Пароль успешно изменён!</h1>';
        echo '<p class="text_center">Теперь Вы можете войти в свой аккаунт.</p>';

        //Подключение подвала
        require_once("footer.php");
    }

}else{
    exit("<p><strong>Ошибка!</strong> Вы зашли на эту страницу напрямую, поэтому нет данных для обработки. Вы можете перейти на <a href=".$address_site."> главную страницу </a>.</p>");
}
?>