<?php

require_once __DIR__ . '/Config/session.php';
session_start();

require_once 'Controllers/GameController.php';
require_once 'Controllers/PageController.php';
require_once 'Controllers/ChoixController.php';
require_once 'Controllers/StatsController.php';
require_once 'Controllers/MatchController.php';
require_once 'Controllers/DialogueController.php';

$action = $_GET['action'] ?? 'accueil';

$actionsAutorisees = [
    'accueil', 'nouvellePartie', 'continuer',
    'page', 'choisir', 'repondreDialogue', 'actionMatch',
    'stats', 'fin', 'sauvegarder', 'recommencer', 'journal'
];

if (!in_array($action, $actionsAutorisees)) $action = 'accueil';

switch ($action) {

    case 'accueil':
        GameController::accueil();
        break;

    case 'nouvellePartie':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') GameController::nouvellePartie();
        else header('Location: index.php?action=accueil');
        break;

    case 'continuer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') GameController::continuer();
        else header('Location: index.php?action=accueil');
        break;

    case 'page':
        PageController::afficher();
        break;

    case 'choisir':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') ChoixController::traiter();
        else header('Location: index.php?action=accueil');
        break;

    case 'repondreDialogue':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') ChoixController::repondreDialogue();
        else header('Location: index.php?action=accueil');
        break;

    case 'actionMatch':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') MatchController::traiter();
        else header('Location: index.php?action=accueil');
        break;

    case 'stats':
        StatsController::afficher();
        break;

    case 'fin':
        GameController::fin();
        break;

    case 'sauvegarder':
        GameController::sauvegarder();
        break;

    case 'recommencer':
        GameController::recommencer();
        break;

    case 'journal':
        GameController::journal();
        break;
}