<?php
require_once 'config.php';
require_once 'function.php';

session_start();


$dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['name'] . ";charset=utf8";
$db = new PDO( $dsn, $dbConfig['user'], $dbConfig['pass'] );


if( $page['premium'] && (!isset($_SESSION ['auth']) || !$_SESSION['user']['premium'] ){
    header('Location: premium.php');
    exit();
    
}

if( $page['admin'] && (!isset($_SESSION ['auth']) || !$_SESSION['user']['admin'] ){
    echo "Vous devez être admin pour accéder a cette section";
    exit();
}

if( !isset( $_SESSION['auth'] ) && isset( $_COOKIE['autoauth'] ) ){
    $keychain = $_COOKIE['autoauth'];
    $userId = explode( '____', $keychain )[0];

    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->bindValue( 1, htmlspecialchars( $userId ) );
    $stmt->execute();
    $user = $stmt->fetch();

    if( $user ){
        $validator = generateCookieKeychain( $user );

        if( $keychain === $validator ){
            login( $user );
        }
    }
}









//
