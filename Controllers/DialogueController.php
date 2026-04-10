<?php
// controllers/DialogueController.php

require_once('Models/Dialogue.php');
require_once('Models/Partie.php');
require_once('Models/Stats.php');
require_once('Models/Page.php');
require_once('Models/Historique.php');

class DialogueController {

    /**
     * Traite la réponse choisie dans un dialogue
     * POST /index.php?action=repondre
     * Paramètres POST : id_reponse
     */
    public static function repondre(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;
        if (!$idPartie) { header('Location: index.php?action=accueil'); exit; }

        $idReponse = (int)($_POST['id_reponse'] ?? 0);
        if ($idReponse === 0) { header('Location: index.php?action=accueil'); exit; }

        $reponse = Dialogue::getReponseById($idReponse);
        if (!$reponse) { header('Location: index.php?action=accueil'); exit; }

        // Appliquer les effets (stats + affinité)
        Dialogue::appliquerEffetsReponse($reponse, $idPartie);

        // XP pour participation au dialogue
        Stats::ajouterXP($idPartie, 5);

        // Rediriger vers la page suivante si définie
        if (!empty($reponse['id_page_suivante'])) {
            $idPageCible = (int)$reponse['id_page_suivante'];
            $pageCible   = Page::getById($idPageCible);
            $estFin      = $pageCible && (bool)$pageCible['est_fin'];
            Partie::majPage($idPartie, $idPageCible, $estFin);
            header('Location: index.php?action=page&id=' . $idPageCible); exit;
        }

        // Sinon rester sur la page actuelle
        $partie = Partie::getById($idPartie);
        header('Location: index.php?action=page&id=' . $partie['page_actuelle']); exit;
    }
}