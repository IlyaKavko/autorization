<?php
session_start();
class DataBase
{

    private $root;     // данная переменная хранит в себе путь к xml файлу
    private $node;     // данная переменная хранит в себе узел для работы с xml файлом
    private $check;    // проверяет на успех авторизации
    private $check_error;         // проверяет ошибки
    private $succes = array();    // записывает в себя успех авторизации
    private $error = array();     // записывает в сябя ошибки 

    public function __construct()
    {
        $this->connenct();
    }

    private function connenct() //подключение к xml файлу
    {

        $this->root = simplexml_load_file("../DataBase/users.xml");
        return $this;
    }

    public function create($email, $name, $login, $hash)  // данная функция служит для регистрации
    {
        foreach ($this->root->user as $resalt) {  // проверка на совподение пользователей в xml

            if ($resalt->login == $login) {
                $this->error['ERROR_LOGIN_CHECK'] = 'ERROR_LOGIN_CHEK';
                $this->error['ERROR_LOGIN_CHECK_MESSAGE'] = 'Пользователь с таким логином уже есть';
                $this->check_error = true;
            }
            if ($resalt->email == $email) {
                $this->error['ERROR_EMAIL_CHECK'] = 'ERROR_EMAIL_CHECK';
                $this->error['ERROR_EMAIL_CHECK_MESSAGE'] = 'Пользователь с таким email уже есть';
                $this->check_error = true;
            }
        }

        if ($this->check_error) {       // если true отправляет json 
            echo json_encode($this->error);
            exit;
        }
        // создает тег user в xml
        $this->node = $this->root->addChild('user');

        //создаю теги email, name, login, password, в теге user и записываю данный пришедшие из формы
        $this->node->addChild('email', $email);
        $this->node->addChild('name', $name);
        $this->node->addChild('login', $login);
        $this->node->addChild('password', $hash);

        // сохраняю полученные данные в xml
        $this->root->asXML("../DataBase/users.xml");
    }

    public function read($login, $password)   //данная функция служит для авторизации
    {

        function generateCode($length = 6)  // гениратор чисел для сессии
        {

            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";

            $code = "";

            $clen = strlen($chars) - 1;
            while (strlen($code) < $length) {

                $code .= $chars[mt_rand(0, $clen)];
            }

            return $code;
        };

        if (!isset($_SESSION['hash_inter'])) {   // проверка есть ли ссессия

            foreach ($this->root->user as $user) {

                if ($user->login == $login and $user->password == $password) {
                    $hash = md5(generateCode(10));            // генирирую ключ для сессии
                    $user->addChild('hash_inter', $hash);     // записывю ключ к пользователю который авторизовался
                    $this->root->asXML("../DataBase/users.xml");    // сохраняю ключ в xml
                    setcookie('name', $user->name, time() + 60 * 60 * 24 * 30);    // создаю куки и сессии
                    setcookie('hash_inter', $user->hash_inter, time() + 60 * 60 * 24 * 30);
                    $_SESSION['name'] = $_COOKIE['name'];
                    $_SESSION['hash_inter'] = $_COOKIE['hash_inter'];

                    $this->succes['SESSION_SUCCES'] = 'SESSION_SUCCES'; // формирую ответ json
                    $this->succes['SESSION_SUCCES_MESSAGE'] = "<div class='alert alert-success' role='alert'>Добро пожаловать $user->name ! Чтобы выйти нажмите <a href='/form/exit.php' class='alert-link'>Выйти</a> </div>";
                    $this->check = true;
                } else {
                    $this->error['ERROR_NOT_FOUND'] = 'ERROR_NOT_FOUND';
                    $this->error['ERROR_NOT_FOUND_MESSAGE'] = 'Неверный логин или пароль';
                    $this->check_error = true;
                }
            }
        }
        // ответ для успешной авторизации
        if ($this->check) {
            echo json_encode($this->succes);
            exit;
        }
        // ответ на ошибки
        if ($this->check_error) {
            echo json_encode($this->error);
            exit;
        }
    }

    public function delete()     // функция служит для удаления ключа авторизации из xml
    {
        $xmlstr = utf8_encode(file_get_contents('../DataBase/users.xml'));
        $doc = new DOMDocument();
        $doc->loadXML($xmlstr);
        $xpath = new DOMXpath($doc);
        $entries = $xpath->query('//hash_inter');
        foreach ($entries as $entry) {
            $entry->parentNode->removeChild($entry);
        }
        $users = $doc->saveXML();
        $doc->save('../DataBase/users.xml');
    }
}
