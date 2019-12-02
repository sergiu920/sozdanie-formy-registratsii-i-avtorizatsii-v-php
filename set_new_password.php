<?php
//Добавляем файл подключения к БД
require_once("dbconnect.php");

//Проверяем, если существует переменная token в глобальном массиве GET
if(isset($_GET['token']) && !empty($_GET['token'])){
    $token = $_GET['token'];
}else{
    exit("<p><strong>Ошибка!</strong> Отсутствует проверочный код.</p>");
}

//Проверяем, если существует переменная email в глобальном массиве GET
if(isset($_GET['email']) && !empty($_GET['email'])){
    $email = $_GET['email'];
}else{
    exit("<p><strong>Ошибка!</strong> Отсутствует адрес электронной почты.</p>");
}

//Делаем запрос на выборке токена из таблицы confirm_users
$query_select_user = $mysqli->query("SELECT reset_password_token FROM `users` WHERE `email` = '".$email."'");

//Если такой пользователь существует
if($query_select_user->num_rows == 1){

    //Если ошибок в запросе нет
    if(($row = $query_select_user->fetch_assoc()) != false){

        //Проверяем совпадает ли token
        if($token == $row['reset_password_token']){

            //(1) Место для вывода формы установки нового пароля

        }else{
            exit("<p><strong>Ошибка!</strong> Неправильный проверочный код.</p>");
        }

    }else{
        //Иначе, если есть ошибки в запросе к БД
        exit("<p><strong>Ошибка!</strong> Сбой при выборе пользователя из БД. </p>");
    }
    
}else{
    exit("<p><strong>Ошибка!</strong> Такой пользователь не зарегистрирован </p>");
}



// Завершение запроса выбора пользователя из таблицы users
$query_select_user->close();

//Закрываем подключение к БД
$mysqli->close();
?>