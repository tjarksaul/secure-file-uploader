<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1);

require_once 'config.php';

header("Content-type: application/json");

if (isset($_GET['check'])) {
    print json_encode(["login" => empty($_SESSION['login']) ? false : !!$_SESSION['login']]);
    exit;
} elseif (isset($_GET['login'])) {
    $password = trim($_POST['password']);

    include 'PasswordHash.php';
    $hasher = new PasswordHash(10, false);
    if ($hasher->checkPassword($password, PASSWORD_HASH)) {
        print json_encode(["code" => 0, "message" => "Login successful"]);
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['login'] = true;
        exit;
    } else {
        print json_encode(["code" => -1, "message" => "Wrong password"]);
        exit;
    }
}
