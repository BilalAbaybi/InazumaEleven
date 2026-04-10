<?php
// models/Stats.php

require_once 'Config/database.php';

class Stats {

    const STATS_VALIDES = ['courage','technique','stamina','vitesse','chance','leadership'];
    const XP_PAR_NIVEAU = 100;

    public static function creer(int $idPartie): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'INSERT INTO stats_joueur
             (id_partie, courage, technique, stamina, vitesse, chance, leadership, xp_total, niveau)
             VALUES (:id, 1, 1, 2, 1, 1, 1, 0, 1)'
        );
        $stmt->execute([':id' => $idPartie]);
    }

    public static function getByPartie(int $idPartie): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM stats_joueur WHERE id_partie = :id');
        $stmt->execute([':id' => $idPartie]);
        return $stmt->fetch();
    }

    public static function modifier(int $idPartie, string $stat, int $delta): void {
        if (!in_array($stat, self::STATS_VALIDES)) return;
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "UPDATE stats_joueur
             SET $stat = GREATEST(0, LEAST(5, $stat + :delta))
             WHERE id_partie = :id"
        );
        $stmt->execute([':delta' => $delta, ':id' => $idPartie]);
    }

    // Applique plusieurs effets depuis un JSON ex: {"courage":1,"stamina":-1}
    public static function appliquerJSON(int $idPartie, ?string $effetsJson): void {
        if (empty($effetsJson)) return;
        $effets = json_decode($effetsJson, true);
        if (!$effets) return;
        foreach ($effets as $stat => $delta) {
            if (in_array($stat, self::STATS_VALIDES)) {
                self::modifier($idPartie, $stat, (int)$delta);
            }
        }
    }

    // Ajoute de l'XP et gère la montée de niveau
    // Retourne true si montée de niveau
    public static function ajouterXP(int $idPartie, int $xp): bool {
        $pdo  = getDB();
        $pdo->prepare('UPDATE stats_joueur SET xp_total = xp_total + :xp WHERE id_partie = :id')
            ->execute([':xp' => $xp, ':id' => $idPartie]);

        $stats         = self::getByPartie($idPartie);
        $niveauAtteint = (int)floor($stats['xp_total'] / self::XP_PAR_NIVEAU) + 1;

        if ($niveauAtteint > (int)$stats['niveau']) {
            $pdo->prepare('UPDATE stats_joueur SET niveau = :n WHERE id_partie = :id')
                ->execute([':n' => $niveauAtteint, ':id' => $idPartie]);
            return true;
        }
        return false;
    }

    public static function verifieCondition(
        int $idPartie,
        int $courageMin    = 0,
        int $techniqueMin  = 0,
        int $staminaMin    = 0,
        int $vitesseMin    = 0,
        int $chanceMin     = 0,
        int $leadershipMin = 0
    ): bool {
        $stats = self::getByPartie($idPartie);
        if (!$stats) return false;
        return $stats['courage']    >= $courageMin
            && $stats['technique']  >= $techniqueMin
            && $stats['stamina']    >= $staminaMin
            && $stats['vitesse']    >= $vitesseMin
            && $stats['chance']     >= $chanceMin
            && $stats['leadership'] >= $leadershipMin;
    }

    // Score de combat pondéré pour les matchs
    public static function getScoreCombat(int $idPartie): float {
        $stats = self::getByPartie($idPartie);
        if (!$stats) return 0;
        return round(
            $stats['technique'] * 0.30 +
            $stats['vitesse']   * 0.20 +
            $stats['courage']   * 0.25 +
            $stats['stamina']   * 0.15 +
            $stats['chance']    * 0.10,
            2
        );
    }
}