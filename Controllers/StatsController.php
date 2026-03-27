<?php

require_once '../InazumaEleven/Models/Stats.php';
require_once '../InazumaEleven/Models/Inventaire.php';

class StatsController {

    /**
     * Retourne les stats du joueur en JSON (pour une future requête AJAX si besoin)
     * GET /index.php?action=stats
     */
    public static function afficher(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;

        if (!$idPartie) {
            http_response_code(401);
            echo json_encode(['erreur' => 'Pas de partie en cours']);
            exit;
        }

        $stats      = Stats::getByPartie($idPartie);
        $inventaire = Inventaire::getByPartie($idPartie);

        header('Content-Type: application/json');
        echo json_encode([
            'stats'      => $stats,
            'inventaire' => $inventaire,
        ]);
        exit;
    }
}