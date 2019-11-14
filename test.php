<?php

//Вывод PHP ошибок
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// несколько получателей
$to = 'sergiu920@mail.ru'; // обратите внимание на запятую
$email_admin = "sergiu1607@gmail.com";

// тема письма
//$subject = 'Test from sozdatisite';
$subject = "Подтверждение почты на сайте ".$_SERVER['HTTP_HOST'];

// текст письма
/*$message = '
<html>
<head>
  <title>Birthday Reminders for August</title>
</head>
<body>
  <p>Here are the birthdays upcoming in August!</p>
  <table>
    <tr>
      <th>Person</th><th>Day</th><th>Month</th><th>Year</th>
    </tr>
    <tr>
      <td>Johny</td><td>10th</td><td>August</td><td>1970</td>
    </tr>
    <tr>
      <td>Sally</td><td>17th</td><td>August</td><td>1973</td>
    </tr>
  </table>
</body>
</html>
';*/

/*$today = date("d.m.Y", time());
var_dump($today);
*/
$message = "
  asd
";

/*$message = "
  <p>Здравствуйте!</p>
  <p>Сегодня </p>
";*/

//$message = str_replace("\n.", "\n..", $message);

// Для отправки HTML-письма должен быть установлен заголовок Content-type
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";

// Дополнительные заголовки
// $headers .= 'From: Admin sozdatisite.ru <sergiu1607@gmail.com>' . "\r\n";
// $headers .= 'Reply-to: sergiu1607@gmail.com' . "\r\n";

$headers .= "From: Admin <$email_admin>" . "\r\n";
$headers .= "Reply-to: $email_admin" . "\r\n";


// Отправляем
$result = mail($to, $subject, $message, $headers);

if (!$result) {
    $errors = error_get_last()['message'];
    print_r($errors);
}

var_dump($result);
var_dump($headers);

/*$email = "sergiu920@mail.ru";

$subject = "Подтверждение почты на сайте ".$_SERVER['HTTP_HOST'];
$subject = "=?utf-8?B?".base64_encode($subject)."?=";



//Составляем тело сообщения
$message = "Здравствуйте! Сегодня\r\n";

// На случай если какая-то строка письма длиннее 70 символов мы используем wordwrap()
$message = wordwrap($message, 70, "\r\n");

//Составляем дополнительные заголовки для почтового сервиса mail.ru
//Переменная $email_admin, объявлена в файле dbconnect.php
$headers = "FROM: sergiu1607@mail.ru\r\nReply-to: sergiu1607@mail.ru\r\nContent-type: text/html; charset=utf-8\r\n";


$result_mail = mail($email, $subject, $message, $headers);

var_dump($result_mail);

var_dump($email);
echo "<br />";
var_dump($subject);
echo "<br />";
var_dump($message);
echo "<br />";
var_dump($headers);*/