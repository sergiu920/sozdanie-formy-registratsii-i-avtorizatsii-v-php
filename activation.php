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

//Удаляем пользователей с таблицы users, которые не подтвердили свою почту в течении сутки
$query_delete_users = $mysqli->query("DELETE FROM `users` WHERE `email_status` = 0 AND `date_registration` < ( NOW() - INTERVAL 1 DAY )");
if(!$query_delete_users){
    exit("<p><strong>Ошибка!</strong> Сбой при удалении просроченного аккаунта. Код ошибки: ".$mysqli->errno."</p>");
}


//Удаляем пользователей из таблицы confirm_users, которые не подтвердили свою почту в течении сутки
$query_delete_confirm_users = $mysqli->query("DELETE FROM `confirm_users` WHERE `date_registration` < ( NOW() - INTERVAL 1 DAY)");
if(!$query_delete_confirm_users){
    exit("<p><strong>Ошибка!</strong> Сбой при удалении просроченного аккаунта(confirm). Код ошибки: ".$mysqli->errno."</p>");
}

//Делаем запрос на выборке токена из таблицы confirm_users
$query_select_user = $mysqli->query("SELECT `token` FROM `confirm_users` WHERE `email` = '".$email."'");

//Если такой пользователь существует, то подтверждаем его почту
if($query_select_user->num_rows == 1){

    //Если ошибок в запросе нет
    if(($row = $query_select_user->fetch_assoc()) != false){

        //Проверяем совпадает ли token
        if($token == $row['token']){

            //(1) Место для следующего куска кода
            //Обновляем статус почтового адреса
            $query_update_user = $mysqli->query("UPDATE `users` SET `email_status` = 1 WHERE `email` = '".$email."'");

            if(!$query_update_user){

                exit("<p><strong>Ошибка!</strong> Сбой при обновлении статуса пользователя. Код ошибки: ".$mysqli->errno."</p>");

            }else{

                //Удаляем данные пользователя из временной таблицы confirm_users
                $query_delete = $mysqli->query("DELETE FROM `confirm_users` WHERE `email` = '".$email."'");

                if(!$query_delete){

                    exit("<p><strong>Ошибка!</strong> Сбой при удалении данных пользователя из временной таблицы. Код ошибки: ".$mysqli->errno."</p>");

                }else{

                    //Подключение шапки
                    require_once("header.php");

                    //Выводим сообщение о том, что почта успешно подтверждена.
                    echo '<h1 class="success_message text_center">Почта успешно подтверждена!</h1>';
                    echo '<p class="text_center">Теперь Вы можете войти в свой аккаунт.</p>';

                    //Подключение подвала
                    require_once("footer.php");
                }
            }

        }else{ //if($token == $row['token'])
            exit("<p><strong>Ошибка!</strong> Неправильный проверочный код.</p>");
        }

    }else{ //if(($row = $query_select_user->fetch_assoc()) != false)
        exit("<p><strong>Ошибка!</strong> Сбой при выборе пользователя из БД. </p>");
    }

}else{ // if($query_select_user->num_rows == 1)
    exit("<p><strong>Ошибка!</strong> Такой пользователь не зарегистрирован. Ваш аккаунт был удален из за того что вы не подтвердили свою почту в течении 24 часов с момента регистрации </p><p>Перейти на <a href=".$address_site."> главную страницу </a> </p>");

}

//Закрываем подключение к БД
$mysqli->close();