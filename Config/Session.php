<?php
// Config/session.php
// À inclure EN PREMIER dans index.php, avant session_start()

$duree = 60 * 60 * 24 * 30; // 30 jours

ini_set('session.gc_maxlifetime', $duree);
ini_set('session.cookie_lifetime', $duree);

session_set_cookie_params([
    'lifetime' => $duree,
    'path'     => '/',
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Strict',
]);