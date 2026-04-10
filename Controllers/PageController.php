<?php
// controllers/PageController.php

require_once('Models/Page.php');
require_once('Models/Choix.php');
require_once('Models/Stats.php');
require_once('Models/Inventaire.php');
require_once('Models/Historique.php');
require_once('Models/Partie.php');
require_once('Models/Affinite.php');
require_once('Models/Dialogue.php');
require_once('Models/MatchSimule.php');
require_once('Models/Journal.php');
require_once('Models/SceneSecrete.php');

class PageController {

    public static function afficher(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;
        $pseudo   = $_SESSION['pseudo']    ?? 'Joueur';

        if (!$idPartie) {
            header('Location: index.php?action=accueil'); exit;
        }

        $idPage = (int)($_GET['id'] ?? 1);

        // Blocage retour arrière
        // Exceptions : ?d= (dialogue), ?tour= (match en cours)
        $estNavigationDialogue = isset($_GET['d']);
        $estNavigationMatch    = isset($_GET['tour']);
        $partie = Partie::getById($idPartie);

        if (!$partie || (int)$partie['page_actuelle'] !== $idPage) {
            if ($estNavigationDialogue) {
                $idPage = (int)$partie['page_actuelle'];
            } elseif ($estNavigationMatch) {
                $pageActuelle = (int)($partie['page_actuelle'] ?? 1);
                header('Location: index.php?action=page&id=' . $pageActuelle
                    . '&tour=' . (int)($_GET['tour'] ?? 1)
                    . '&sj='   . (int)($_GET['sj']   ?? 0)
                    . '&sa='   . (int)($_GET['sa']   ?? 0));
                exit;
            } else {
                header('Location: index.php?action=page&id=' . ($partie['page_actuelle'] ?? 1)); exit;
            }
        }

        $page = Page::getById($idPage);
        if (!$page) { header('Location: index.php?action=accueil'); exit; }

        // Remplacement du pseudo
        $page['texte'] = str_replace('Takamura', htmlspecialchars($pseudo), $page['texte']);
        $page['titre'] = str_replace('Takamura', htmlspecialchars($pseudo), $page['titre']);

        // Scène secrète
        $pageSecrete = SceneSecrete::verifier($idPage, $idPartie);
        if ($pageSecrete) {
            Partie::majPage($idPartie, (int)$pageSecrete['id_page']);
            header('Location: index.php?action=page&id=' . $pageSecrete['id_page']); exit;
        }

        // Événement aléatoire
        $evenement = EvenementAleatoire::tenter($idPage, $idPartie);
        if ($evenement['declenche']) {
            $_SESSION['event_aleatoire'] = $evenement['texte'];
        }

        // Page de fin
        if ($page['est_fin']) {
            Partie::terminer($idPartie, $page['type_fin'] ?? 'defaite');
            header('Location: index.php?action=fin'); exit;
        }

        // Journal
        if (!empty($page['resume'])) {
            Journal::ajouter($idPartie, $idPage, str_replace('Takamura', $pseudo, $page['resume']));
        }

        // Image
        $imgPage = !empty($page['image']) ? $page['image'] : null;

        // Données communes
        $stats      = Stats::getByPartie($idPartie);
        $inventaire = Inventaire::getByPartie($idPartie);
        $affinites  = Affinite::getAll($idPartie);
        if (empty($affinites)) {
            Affinite::initialiser($idPartie);
            $affinites = Affinite::getAll($idPartie);
        }

        $choixDisponibles = [];
        $dialogues        = [];
        $matchConfig      = null;
        $actionsMatch     = [];

        switch ($page['type_page']) {
            case 'dialogue':
                $dialogues = Dialogue::getByPage($idPage);
                foreach ($dialogues as &$dial) {
                    $dial['texte'] = str_replace('Takamura', $pseudo, $dial['texte']);
                }
                unset($dial);
                if (!empty($dialogues)) {
                    $dernierDialogue  = end($dialogues);
                    $choixDisponibles = Dialogue::getReponses(
                        (int)$dernierDialogue['id_dialogue'],
                        $idPartie
                    );
                }
                break;

            case 'match':
                $matchConfig = MatchSimule::getByPage($idPage);
                if ($matchConfig) {
                    $actionsMatch = MatchSimule::getActions(
                        (int)$matchConfig['id_match'],
                        $idPartie
                    );
                }
                $choixDisponibles = Choix::getDisponibles($idPage, $idPartie);
                break;

            default:
                $choixDisponibles = Choix::getDisponibles($idPage, $idPartie);
                break;
        }

        require 'Views/page.php';
    }
}