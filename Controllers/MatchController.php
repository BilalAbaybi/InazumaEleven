<?php
// controllers/MatchController.php

require_once('Models/MatchSimule.php');
require_once('Models/Stats.php');
require_once('Models/Partie.php');
require_once('Models/Page.php');
require_once('Models/Journal.php');

class MatchController {

    public static function traiter(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;
        if (!$idPartie) { header('Location: index.php?action=accueil'); exit; }

        $idMatch      = (int)($_POST['id_match']     ?? 0);
        $idAction     = (int)($_POST['id_action']    ?? 0);
        $scoreJoueur  = (int)($_POST['score_joueur'] ?? 0);
        $scoreAdverse = (int)($_POST['score_adverse']?? 0);
        $tour         = (int)($_POST['tour']         ?? 1);

        $matchConfig = MatchSimule::getByPage(
            (int)Partie::getById($idPartie)['page_actuelle']
        );
        if (!$matchConfig || (int)$matchConfig['id_match'] !== $idMatch) {
            header('Location: index.php?action=accueil'); exit;
        }

        $actions = MatchSimule::getActions($idMatch, $idPartie);
        $action  = null;
        foreach ($actions as $a) {
            if ((int)$a['id_action'] === $idAction) { $action = $a; break; }
        }
        if (!$action) { header('Location: index.php?action=accueil'); exit; }

        // Simuler — max 1 but par tour
        $resultatAction = MatchSimule::simulerAction($action, $idPartie);
        if ($resultatAction['succes']) {
            $scoreJoueur += 1;
        }

        // L'adversaire ne marque que si le joueur a raté
        $statsAdverse   = json_decode($matchConfig['stats_adversaire'], true) ?? [];
        $moyenneAdverse = !empty($statsAdverse) ? array_sum($statsAdverse) / count($statsAdverse) : 2;
        $probaAdverse   = (int)(($moyenneAdverse / 5) * 50);
        $adversaireMarque = false;
        if (!$resultatAction['succes'] && rand(1, 100) <= $probaAdverse) {
            $scoreAdverse++;
            $adversaireMarque = true;
        }

        // Stats pour feedback
        $stats      = Stats::getByPartie($idPartie);
        $statNom    = $action['stat_utilisee'];
        $statValeur = $stats[$statNom] ?? 1;

        $texteResultat = $resultatAction['texte'];
        if ($adversaireMarque) {
            $texteResultat .= ' ' . htmlspecialchars($matchConfig['nom_adversaire']) . ' contre-attaque et marque !';
        }

        $_SESSION['match_resultat_action'] = [
            'succes'      => $resultatAction['succes'],
            'texte'       => $texteResultat,
            'stat_nom'    => $statNom,
            'stat_valeur' => $statValeur,
            'proba'       => $resultatAction['proba'] ?? 0,
        ];

        $toursMax = 5;
        if ($tour >= $toursMax) {
            self::terminerMatch($idPartie, $matchConfig, $scoreJoueur, $scoreAdverse);
            return;
        }

        header('Location: index.php?action=page&id=' . $matchConfig['id_page']
             . '&tour=' . ($tour + 1)
             . '&sj='   . $scoreJoueur
             . '&sa='   . $scoreAdverse);
        exit;
    }

    private static function terminerMatch(
        int $idPartie,
        array $matchConfig,
        int $scoreJoueur,
        int $scoreAdverse
    ): void {
        $resultat = MatchSimule::determinerResultat(
            $matchConfig['id_match'],
            $scoreJoueur,
            $scoreAdverse
        );

        MatchSimule::enregistrer($idPartie, $matchConfig['id_match'], $scoreJoueur, $scoreAdverse, $resultat);

        $idPageCible = match($resultat) {
            'victoire' => (int)$matchConfig['id_page_victoire'],
            'defaite'  => (int)$matchConfig['id_page_defaite'],
            'nul'      => (int)($matchConfig['id_page_nul'] ?? $matchConfig['id_page_defaite']),
        };

        // Appliquer bonus seulement si la page cible n'a pas encore été visitée
        if (!Journal::pageDejaVisitee($idPartie, $idPageCible)) {
            if ($resultat === 'victoire' && !empty($matchConfig['bonus_victoire'])) {
                Stats::appliquerJSON($idPartie, $matchConfig['bonus_victoire']);
                Stats::ajouterXP($idPartie, 50);
            } elseif ($resultat === 'defaite') {
                Stats::modifier($idPartie, 'stamina', -1);
                Stats::ajouterXP($idPartie, 10);
            } else {
                Stats::ajouterXP($idPartie, 25);
            }
        }

        $pageCible = Page::getById($idPageCible);
        $estFin    = $pageCible && (bool)$pageCible['est_fin'];
        Partie::majPage($idPartie, $idPageCible, $estFin);

        $_SESSION['match_fin'] = [
            'resultat'      => $resultat,
            'score_joueur'  => $scoreJoueur,
            'score_adverse' => $scoreAdverse,
            'adversaire'    => $matchConfig['nom_adversaire'],
        ];

        header('Location: index.php?action=page&id=' . $idPageCible); exit;
    }
}