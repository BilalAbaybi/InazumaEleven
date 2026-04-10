<?php
// models/MatchSimule.php

require_once('Config/database.php');
require_once ('Models/Stats.php');

class MatchSimule {

    // Récupère la configuration d'un match lié à une page
    public static function getByPage(int $idPage): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM match_config WHERE id_page = :id');
        $stmt->execute([':id' => $idPage]);
        return $stmt->fetch();
    }

    // Récupère les actions disponibles pour un match
    public static function getActions(int $idMatch, int $idPartie): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT * FROM action_match WHERE id_match = :id ORDER BY id_action'
        );
        $stmt->execute([':id' => $idMatch]);
        $toutes = $stmt->fetchAll();

        // Filtrer selon les conditions (objet requis, etc.)
        $disponibles = [];
        foreach ($toutes as $action) {
            if (self::verifieConditionAction($action, $idPartie)) {
                $disponibles[] = $action;
            }
        }
        return $disponibles;
    }

    // Simule le résultat d'une action
    // Retourne ['succes' => bool, 'texte' => string, 'points' => int]
    public static function simulerAction(array $action, int $idPartie): array {
        $stats = Stats::getByPartie($idPartie);
        if (!$stats) return ['succes' => false, 'texte' => 'Erreur de stats.', 'points' => 0];

        $statJoueur = $stats[$action['stat_utilisee']] ?? 1;

        // Formule : probabilité de succès basée sur la stat + aléatoire
        // stat 1 = 40% de base, stat 5 = 90% de base
        // La chance du joueur ajoute jusqu'à +10%
        $probaBase  = 30 + ($statJoueur * 12);
        $bonusChance = ($stats['chance'] - 1) * 2; // -2% à +8%
        $proba      = min(95, $probaBase + $bonusChance);

        $tirage = rand(1, 100);
        $succes = $tirage <= $proba;

        return [
            'succes' => $succes,
            'texte'  => $succes
                ? ($action['texte_succes']  ?? 'Action réussie !')
                : ($action['texte_echec']   ?? 'Action ratée...'),
            'points' => $succes ? (int)$action['points_succes'] : 0,
            'proba'  => $proba,  // pour debug si besoin
        ];
    }

    // Détermine le résultat final d'un match
    // Retourne 'victoire', 'defaite' ou 'nul'
    public static function determinerResultat(
        int $idMatch,
        int $scoreJoueur,
        int $scoreAdverse
    ): string {
        if ($scoreJoueur > $scoreAdverse) return 'victoire';
        if ($scoreJoueur < $scoreAdverse) return 'defaite';
        return 'nul';
    }

    // Enregistre le résultat d'un match en historique
    public static function enregistrer(
        int $idPartie,
        int $idMatch,
        int $scoreJoueur,
        int $scoreAdverse,
        string $resultat
    ): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'INSERT INTO historique_match
             (id_partie, id_match, score_joueur, score_adverse, resultat)
             VALUES (:partie, :match, :sj, :sa, :res)'
        );
        $stmt->execute([
            ':partie' => $idPartie,
            ':match'  => $idMatch,
            ':sj'     => $scoreJoueur,
            ':sa'     => $scoreAdverse,
            ':res'    => $resultat,
        ]);
    }

    // Calcule le score adverse selon ses stats JSON
    // Retourne un score entre 1 et 5
    public static function scoreAdverse(array $matchConfig): int {
        $stats = json_decode($matchConfig['stats_adversaire'], true) ?? [];
        if (empty($stats)) return 2;

        $moyenne = array_sum($stats) / count($stats);
        // Score adverse = moyenne des stats / max (5) * seuil de victoire * facteur aléatoire
        $facteur = rand(80, 120) / 100; // ±20% d'aléatoire
        return (int)round(($moyenne / 5) * $matchConfig['seuil_victoire'] * $facteur);
    }

    // Récupère l'historique des matchs d'une partie
    public static function getHistorique(int $idPartie): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT h.*, m.nom_adversaire
             FROM historique_match h
             JOIN match_config m ON m.id_match = h.id_match
             WHERE h.id_partie = :id
             ORDER BY h.date_match ASC'
        );
        $stmt->execute([':id' => $idPartie]);
        return $stmt->fetchAll();
    }

    // Vérifie si une action est accessible (condition_json)
    private static function verifieConditionAction(array $action, int $idPartie): bool {
        if (empty($action['condition_json'])) return true;
        $cond = json_decode($action['condition_json'], true);
        if (!$cond) return true;

        require_once __DIR__ . '/Inventaire.php';
        if (!empty($cond['objet_requis'])) {
            if (!Inventaire::possede($idPartie, (int)$cond['objet_requis'])) return false;
        }
        return true;
    }
}