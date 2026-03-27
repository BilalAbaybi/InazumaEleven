<?php

require_once '../InazumaEleven/Config/database.php';

class Stats {

    // Crée les stats initiales d'une partie
    public static function creer(int $idPartie): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'INSERT INTO stats_joueur (id_partie, courage, technique, stamina)
             VALUES (:id, 1, 1, 2)'
        );
        $stmt->execute([':id' => $idPartie]);
    }

    // Récupère les stats d'une partie
    public static function getByPartie(int $idPartie): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT * FROM stats_joueur WHERE id_partie = :id'
        );
        $stmt->execute([':id' => $idPartie]);
        return $stmt->fetch();
    }

    // Met à jour une stat spécifique (courage, technique ou stamina)
    // La valeur est clampée entre 0 et 3
    public static function modifier(int $idPartie, string $stat, int $delta): void {
        $statsAuthorisees = ['courage', 'technique', 'stamina'];
        if (!in_array($stat, $statsAuthorisees)) return;

        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "UPDATE stats_joueur
             SET $stat = GREATEST(0, LEAST(3, $stat + :delta))
             WHERE id_partie = :id"
        );
        $stmt->execute([':delta' => $delta, ':id' => $idPartie]);
    }

    // Vérifie si le joueur remplit une condition de stats
    public static function verifieCondition(
        int $idPartie,
        int $courageMin,
        int $techniqueMin,
        int $staminaMin
    ): bool {
        $stats = self::getByPartie($idPartie);
        if (!$stats) return false;

        return $stats['courage']   >= $courageMin
            && $stats['technique'] >= $techniqueMin
            && $stats['stamina']   >= $staminaMin;
    }
}