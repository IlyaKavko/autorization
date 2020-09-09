<?php
session_start();
include "../DataBase/DataBase.php";
$result = json_encode($_POST); // принимаю json 
$data = json_decode($result, true); // Декодирую строку Json

//filter_var убирает разлиные символы которые недолжны находится в bd, trim - убирает пробелы 
$login = filter_var(trim($data['login_aut']));
$password = filter_var(trim($data['password_aut']));

$hash = md5($password); // прячу пароль

$db = new DataBase(); // класс для работы с DataBase
$db->read($login, $hash); // передаю информация для обработки