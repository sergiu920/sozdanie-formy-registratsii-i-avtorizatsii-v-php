<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Подключаем библиотеку PHPMAILER
// Файлы phpmailer
require_once('phpmailer/PHPMailer.php');
require_once('phpmailer/SMTP.php');
require_once('phpmailer/Exception.php');

//Запускаем сессию
session_start();

//Добавляем файл подключения к БД
require_once("dbconnect.php");

//Объявляем ячейку для добавления ошибок, которые могут возникнуть при обработке формы.
$_SESSION["error_messages"] = '';

//Объявляем ячейку для добавления успешных сообщений
$_SESSION["success_messages"] = '';

//Если кнопка Восстановить была нажата
if(isset($_POST["send"])){

    //Проверяем, отправлена ли капча
    if(isset($_POST["captcha"])){

        //(1) Место для следующего куска кода

        //Обрезаем пробелы с начала и с конца строки
        $captcha = trim($_POST["captcha"]);

        if(!empty($captcha)){

            //Сравниваем полученное значение со значением из сессии.
            if(($_SESSION["rand"] != $captcha) && ($_SESSION["rand"] != "")){

                // Если капча не верна, то возвращаем пользователя на страницу восстановления пароля, и там выведем ему сообщение об ошибке что он ввёл неправильную капчу.

                // Сохраняем в сессию сообщение об ошибке.
                $_SESSION["error_messages"] =  "<p class='mesage_error'><strong>Ошибка!</strong> Вы ввели неправильную капчу </p>";

                //Возвращаем пользователя на страницу восстановления пароля
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."/reset_password.php");
                //Останавливаем скрипт
                exit();
            }
        }else{

            // Сохраняем в сессию сообщение об ошибке.
            $_SESSION["error_messages"] = "<p class='mesage_error'><strong>Ошибка!</strong> Поле для ввода капчи не должна быть пустой. </p>";

            //Возвращаем пользователя на страницу восстановления пароля
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/reset_password.php");
            //Останавливаем скрипт
            exit();
        }

        //Обрабатываем полученный почтовый адрес
        if(isset($_POST["email"])){

            //Обрезаем пробелы с начала и с конца строки
            $email = trim($_POST["email"]);

            if(!empty($email)){

                $email = htmlspecialchars($email, ENT_QUOTES);

                //Проверяем формат полученного почтового адреса с помощью регулярного выражения
                $reg_email = "/^[a-z0-9][a-z0-9\._-]*[a-z0-9]*@([a-z0-9]+([a-z0-9-]*[a-z0-9]+)*\.)+[a-z]+/i";

                //Если формат полученного почтового адреса не соответствует регулярному выражению
                if( !preg_match($reg_email, $email)){

                    // Сохраняем в сессию сообщение об ошибке.
                    $_SESSION["error_messages"] = "<p class='mesage_error' >Вы ввели неправильный email</p>";

                    //Возвращаем пользователя на страницу восстановления пароля
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".$address_site."/reset_password.php");

                    //Останавливаем скрипт
                    exit();
                }
            }else{
                // Сохраняем в сессию сообщение об ошибке.
                $_SESSION["error_messages"] = "<p class='mesage_error' > <strong>Ошибка!</strong> Поле для ввода почтового адреса(email) не должна быть пустой.</p>";

                //Возвращаем пользователя на страницу восстановления пароля
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."/reset_password.php");
                //Останавливаем скрипт
                exit();
            }

        }else{
            // Сохраняем в сессию сообщение об ошибке.
            $_SESSION["error_messages"] = "<p class='mesage_error' > <strong>Ошибка!</strong> Отсутствует поле для ввода Email</p>";

            //Возвращаем пользователя на страницу восстановления пароля
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/reset_password.php");

            //Останавливаем скрипт
            exit();
        }

        // (2) Место для составления запроса к БД

        //Запрос к БД на выборке пользователя.
        $result_query_select = $mysqli->query("SELECT email_status FROM `users` WHERE email = '".$email."'");

        if(!$result_query_select){

            // Сохраняем в сессию сообщение об ошибке.
            $_SESSION["error_messages"] = "<p class='mesage_error' > Ошибка запроса на выборки пользователя из БД</p>";

            //Возвращаем пользователя на страницу восстановления пароля
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$address_site."/reset_password.php");

            //Останавливаем скрипт
            exit();

        }else{

            //Проверяем, если в базе нет пользователя с такими данными, то выводим сообщение об ошибке
            if($result_query_select->num_rows == 1){

                //Проверяем, подтвержден ли указанный email
                while(($row = $result_query_select->fetch_assoc()) !=false){

                    // (3) Место для следующего куска кода

                    //Если email не подтверждён
                    if((int)$row["email_status"] === 0){

                        // Сохраняем в сессию сообщение об ошибке.
                        $_SESSION["error_messages"] = "<p class='mesage_error' ><strong>Ошибка!</strong> Вы не можете восстановить свой пароль, потому что указанный адрес электронной почты ($email) не подтверждён. </p><p>Для подтверждения почты перейдите по ссылке из письма, которую получили после регистрации.</p><p><strong>Внимание!</strong> Ссылка для подтверждения почты, действительна 24 часа с момента регистрации. Если Вы не подтвердите Ваш email в течении этого времени, то Ваш аккаунт будет удалён.</p>";

                        //Возвращаем пользователя на страницу восстановления пароля
                        header("HTTP/1.1 301 Moved Permanently");
                        header("Location: ".$address_site."/reset_password.php");

                        //Останавливаем скрипт
                        exit();

                    }else{
                        //Составляем зашифрованный и уникальный token
                        $token=md5($email.time());

                        //Сохраняем токен в БД
                        $query_update_token = $mysqli->query("UPDATE users SET reset_password_token='$token' WHERE email='$email'");

                        if(!$query_update_token){

                            // Сохраняем в сессию сообщение об ошибке.
                            $_SESSION["error_messages"] = "<p class='mesage_error' >Возникла ошибка во время сохранении токена</p><p><strong>Описание ошибки</strong>: ".$mysqli->error."</p>";

                            //Возвращаем пользователя на страницу восстановления пароля
                            header("HTTP/1.1 301 Moved Permanently");
                            header("Location: ".$address_site."/reset_password.php");

                            //Останавливаем  скрипт
                            exit();

                        }else{

                            //Составляем ссылку на страницу установки нового пароля.
                            $link_reset_password = $address_site."/set_new_password.php?email=$email&token=$token";


                            $mail = new PHPMailer(true);
                            $mail->CharSet = "UTF-8";

                            try {

                                //Enable SMTP debugging
                                //$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Включает debug

                                $mail->isSMTP(); // Указываем что необходимо использовать SMTP

                                $mail->Host = 'smtp.gmail.com'; // Указываем SMTP сервер, который будет отправлять письма
                                $mail->SMTPAuth = true; // Включаем SMTP авторизацию
                                $mail->Username = $username_smtp; // Указан в файле dbconnect.php. Ваш логин от почты с которой будут отправляться письма
                                $mail->Password = $password_smtp; // Указан в файле dbconnect.php. Ваш пароль от почты с которой будут отправляться письма

                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // PHPMailer::ENCRYPTION_STARTTLS;, PHPMailer::ENCRYPTION_SMTPS;
                                $mail->Port = 587; // 587 - TLS; 465 - SSL; TCP порт. Этот порт может отличаться у других провайдеров

                                $mail->setFrom($username_smtp); // от кого будет уходить письмо?

                                $mail->addAddress($email); // Кому будет уходить письмо

                                $mail->isHTML(true); // Указываем что будем отправлять в формате HTML

                                $mail->Subject = "Восстановление пароля от сайта ".$_SERVER['HTTP_HOST']; // Составляем заголовок письма

                                //Составляем зашифрованный и уникальный token
                                $token = md5($email.time());

                                //Составляем тело сообщения
                                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                                $message = 'Здравствуйте! <br/> <br/> Для восстановления пароля от сайта <a href="'.$protocol.$_SERVER['HTTP_HOST'].'"> '.$_SERVER['HTTP_HOST'].' </a>, перейдите по этой <a href="'.$link_reset_password.'">ссылке</a>.';

                                $mail->Body = $message;
                                //$mail->AltBody = '';

                                if($mail->send()) {

                                    $_SESSION["success_messages"] = "<p class='success_message' >Ссылка на страницу установки нового пароля, была отправлена на указанный E-mail ($email) </p>";

                                    //Отправляем пользователя на страницу восстановления пароля и убираем форму для ввода email
                                    header("HTTP/1.1 301 Moved Permanently");
                                    header("Location: ".$address_site."/reset_password.php?hidden_form=1");

                                    exit();

                                } else {

                                    $_SESSION["error_messages"] .= "<p class='mesage_error' >Ошибка при отправлении письма на почту ".$email.", с сылкой на страницу установки нового пароля. </p><p> Причина ошибки: {$mail->ErrorInfo} </p>";
                                }

                            } catch (Exception $e) {
                                $_SESSION["error_messages"] .= "<p class='mesage_error' >Exception: Ошибка при отправлении письма с сылкой подтверждения, на почту ".$email." </p><p> Причина ошибки: {$mail->ErrorInfo} </p>";
                            }

                            //Возвращаем пользователя на страницу восстановления пароля
                            header("HTTP/1.1 301 Moved Permanently");
                            header("Location: ".$address_site."/reset_password.php");

                            //Останавливаем скрипт
                            exit();

                        }
                    } // if($row["email_status"] === 0)

                } // End while

            }else{

                // Сохраняем в сессию сообщение об ошибке.
                $_SESSION["error_messages"] = "<p class='mesage_error' ><strong>Ошибка!</strong> Такой пользователь не зарегистрирован</p>";

                //Возвращаем пользователя на страницу восстановления пароля
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."/reset_password.php");

                //Останавливаем скрипт
                exit();
            }
        }

    }else{ //if(isset($_POST["captcha"]))
        //Если капча не передана
        exit("<p><strong>Ошибка!</strong> Отсутствует проверочный код, то есть код капчи. Вы можете перейти на <a href=".$address_site."> главную страницу </a>.</p>");
    }

}else{ //if(isset($_POST["send"]))
    exit("<p><strong>Ошибка!</strong> Вы зашли на эту страницу напрямую, поэтому нет данных для обработки. Вы можете перейти на <a href=".$address_site."> главную страницу </a>.</p>");
}
?>