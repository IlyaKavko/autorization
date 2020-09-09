<?php
session_start();
include "../DataBase/DataBase.php";
$db = new DataBase();
$db->delete();

if (isset($_SERVER['HTTP_COOKIE'])) { //do we have any
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']); //get all cookies
    foreach ($cookies as $cookie) { //loop
        $parts = explode('=', $cookie); //get the bits we need
        $name = trim($parts[0]);
        setcookie($name, '', time() - 1000); //kill it
        setcookie($name, '', time() - 1000, '/'); //kill it more
    }
}

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name('name'),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
    setcookie(
        session_name('hash_inter'),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

ini_set('session.gc_max_lifetime', 0);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
session_destroy();
header('Location: /');
