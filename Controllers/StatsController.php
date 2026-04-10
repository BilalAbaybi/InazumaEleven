<?php
// controllers/StatsController.php

require_once('Models/Stats.php');
require_once('Models/Inventaire.php');
require_once('Models/Affinite.php');

class StatsController {

    // Retourne les stats complètes en JSON (pour AJAX si besoin)
    public static function afficher(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;
        if (!$idPartie) {
            http_response_code(401);
            echo json_encode(['erreur' => 'Pas de partie en cours']);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'stats'      => Stats::getByPartie($idPartie),
            'inventaire' => Inventaire::getByPartie($idPartie),
            'affinites'  => Affinite::getAll($idPartie),
            'score_combat' => Stats::getScoreCombat($idPartie),
        ]);
        exit;
    }
}