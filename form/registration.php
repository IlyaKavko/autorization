<?php
include '../DataBase/DataBase.php';
$result = json_encode($_POST); // принимаю json 
$data = json_decode($result, true); // Декодирую строку Json

//filter_var убирает разлиные символы которые недолжны находится в bd, trim - убирает пробелы 
$email = filter_var(trim($data['email']));
$name = filter_var(trim($data['name']));
$login = filter_var(trim($data['login']));
$password = filter_var(trim($data['password']));
$repete_password = filter_var(trim($data['confirm_password']));

$check;     // данная переменная служит для отлавливания ошибок
$validation = array();  // данная переменая записывает в себя ошибки

if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
    $validation["ERROR_EMAIL"] = "ERROR_EMAIL";
    $validation["ERROR_EMAIL_MESAGE"] = "Введите коректный Email";
    $check = true;    
};

if (!preg_match("/^[a-zA-Z][a-zA-Z0-9]{1,20}$/", $name)) {
    $validation["ERROR_NAME"] = "ERROR_NAME";
    $validation["ERROR_NAME_MESSAGE"] = "минимум 2 символа, только латинские буквы и цифры";
    $check = true;    
}
if (!preg_match("/^[a-zA-Z0-9]{6,}$/", $login)) {
    $validation["ERROR_LOGIN"] = "ERROR_LOGIN";
    $validation["ERROR_LOGIN_MESSAGE"] = "минимум 6 символов, только латинские буквы и цифры";
    $check = true;    
}
if (!preg_match("/(?=^.{6,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)) {
    $validation["ERROR_PASSWORD"] = "ERROR_PASSWORD";
    $validation["ERROR_PASSWORD_MESSAGE"] = "минимум 6 символов , обязательно должны содержать цифру, латинские буквы в разных регистрах и спец символ (знаки)";
    $check = true;
}
if ($password !== $repete_password) {
    $validation["ERROR_CONFIRM_PASSWORD"] = "ERROR_CONFIRM_PASSWORD";
    $validation["ERROR_CONFIRM_PASSWORD_MESSAGE"] = "Пароли не совпадают";
    $check = true;   
}

if ($check) {       // если true значит есть ошибки
    echo json_encode($validation);  // формирует json для отправки
    exit;
}

$hash = md5($password); // прячу пароль

$db = new DataBase(); // класс для работы с DataBase

$db->create($email, $name, $login, $hash); // передаю информация для обработки

$validation['SUCCESS'] = 'SUCCESS'; // если валидация успешно пройдена фармируется ответ json
$validation['SUCCESS_MESSAGE'] = 'Регистрация прошла успешно!';
echo json_encode($validation);
