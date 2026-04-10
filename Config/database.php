<?php

//Connexion a la BDD MariaDB via PDO

define('Hostname', '' );
define('Port', '' );
define('Database', '' );
define('Username', '' );
define('Password', '' );

function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . Hostname
                 . ';port=' . Port
                 . ';dbname=' . Database
                 . ';charset=utf8mb4';
 
            $pdo = new PDO($dsn, Username, Password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('Erreur BDD : ' . $e->getMessage());
            die('Impossible de se connecter à la base de données.');
        }
    }
 
    return $pdo;
}
 