<?php
    session_start();
    // Генерируем случайное число.
    $rand = mt_rand(1000, 9999);

    // Сохраняем значение переменной  $rand ( капчи ) в сессию
    $_SESSION["rand"] = $rand;
 
    //создаём новое черно-белое изображение
    $im = imageCreateTrueColor(90,50);
 
    // Указываем белый цвет для текста
    $c = imageColorAllocate($im, 255, 255, 255);
 
    // Записываем полученное случайное число на изображение
    imageTtfText($im, 20, -10, 10, 30, $c, __DIR__."/fonts/verdana.ttf", $rand);
 
    header("Content-type: image/png");
 
    // Выводим изображение
    imagePng($im);
 
    //Освобождаем ресурсы
    imageDestroy($im);
?>