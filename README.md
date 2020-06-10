# sozdanie-formy-registratsii-i-avtorizatsii-v-php
Форма регистрации и авторизации

<h2>Описание веток</h2>

<p>
  <strong>sozdanie-formy-registratsii-i-avtorizatsii-v-php</strong> - В этой ветке находятся исходники функционала, который был реализован в статье <a href="https://sozdatisite.ru/php/sozdanie-formy-registratsii-i-avtorizatsii-v-php.html">Создание формы регистрации и авторизации</a>
</p>

<h2>Импорт базы данных</h2>

<p>
  1. Заходим в phpmyadmin и создаем базу данных. Название базы данных придумайте сами. 
  2. Выбираем созданную базу данних и переходим на вкладке "Import" и импортируем файл "sozdatisite_id6-article.sql", который находится в репозиторий. 
</p>


<h2>Подключение к базы данных</h2>

<p>
  В корневую папку сайта, создаем файл с названием "dbconnect.php". В этом файле, добавляем следующий код:
  
  ```
    <?php
        // Указываем кодировку
        header('Content-Type: text/html; charset=utf-8');

        $server = "localhost"; /* имя хоста (уточняется у провайдера), если работаем на локальном сервере, то указываем localhost */
        $username = "имя_пользователя_бд"; /* Имя пользователя БД */
        $password = "пароль_пользователя_бд"; /* Пароль пользователя, если у пользователя нет пароля то, оставляем пустым */
        $database = "имя_базы_данных"; /* Имя базы данных, которую создали */

        // Подключение к базе данный через MySQLi
        $mysqli = new mysqli($server, $username, $password, $database);

        // Проверяем, успешность соединения. 
        if ($mysqli->connect_errno) {
                die("<p><strong>Ошибка подключения к БД</strong></p><p><strong>Код ошибки: </strong> ". $mysqli->connect_errno ." </p><p><strong>Описание ошибки:</strong> ".$mysqli->connect_error."</p>");
        }

        // Устанавливаем кодировку подключения
        $mysqli->set_charset('utf8');

        //Для удобства, добавим здесь переменную, которая будет содержать название нашего сайта
        $address_site = "http://testsite.local";
    ?>
  ```
</p>

