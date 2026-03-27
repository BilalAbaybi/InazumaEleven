<?php

session_start();

// --- Chargement des Controllers ---
require_once '../InazumaEleven/Controllers/GameController.php';
require_once '../InazumaEleven/Controllers/PageController.php';
require_once '../InazumaEleven/Controllers/ChoixController.php';
require_once '../InazumaEleven/Controllers/StatsController.php';

// --- Routeur : lecture de l'action dans l'URL ---
$action = $_GET['action'] ?? 'accueil';

// Sécurité : on n'autorise que les actions connues
$actionsAutorisees = ['accueil', 'nouvellePartie', 'page', 'choisir', 'stats', 'fin'];

if (!in_array($action, $actionsAutorisees)) {
    $action = 'accueil';
}

// --- Dispatch vers le bon Controller ---
switch ($action) {

    case 'accueil':
        GameController::accueil();
        break;

    case 'nouvellePartie':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            GameController::nouvellePartie();
        } else {
            header('Location: index.php?action=accueil');
        }
        break;

    case 'page':
        PageController::afficher();
        break;

    case 'choisir':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ChoixController::traiter();
        } else {
            header('Location: index.php?action=accueil');
        }
        break;

    case 'stats':
        StatsController::afficher();
        break;

    case 'fin':
        GameController::fin();
        break;
}