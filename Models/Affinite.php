<?php
// models/Affinite.php

require_once('Config/database.php');

class Affinite {

    // Initialise les affinités à 50 (neutre) pour tous les personnages
    // Appelé à la création d'une nouvelle partie
    public static function initialiser(int $idPartie): void {
        $pdo  = getDB();
        $stmt = $pdo->query('SELECT id_personnage FROM personnage');
        $persos = $stmt->fetchAll();

        $insert = $pdo->prepare(
            'INSERT IGNORE INTO affinite (id_partie, id_personnage, valeur, type)
             VALUES (:partie, :perso, 50, "neutre")'
        );
        foreach ($persos as $p) {
            $insert->execute([
                ':partie' => $idPartie,
                ':perso'  => $p['id_personnage']
            ]);
        }
    }

    // Récupère l'affinité avec un personnage précis
    public static function get(int $idPartie, int $idPersonnage): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT a.*, p.nom, p.image
             FROM affinite a
             JOIN personnage p ON p.id_personnage = a.id_personnage
             WHERE a.id_partie = :partie AND a.id_personnage = :perso'
        );
        $stmt->execute([':partie' => $idPartie, ':perso' => $idPersonnage]);
        return $stmt->fetch();
    }

    // Récupère toutes les affinités d'une partie
    public static function getAll(int $idPartie): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT a.*, p.nom, p.image, p.poste
             FROM affinite a
             JOIN personnage p ON p.id_personnage = a.id_personnage
             WHERE a.id_partie = :partie
             ORDER BY a.valeur DESC'
        );
        $stmt->execute([':partie' => $idPartie]);
        return $stmt->fetchAll();
    }

    // Modifie la valeur d'affinité (clampée entre 0 et 100)
    // Met à jour automatiquement le type selon la valeur
    public static function modifier(int $idPartie, int $idPersonnage, int $delta): void {
        $pdo  = getDB();

        // Mise à jour valeur avec clamp
        $stmt = $pdo->prepare(
            'UPDATE affinite
             SET valeur = GREATEST(0, LEAST(100, valeur + :delta))
             WHERE id_partie = :partie AND id_personnage = :perso'
        );
        $stmt->execute([
            ':delta'  => $delta,
            ':partie' => $idPartie,
            ':perso'  => $idPersonnage
        ]);

        // Mise à jour automatique du type selon la valeur
        $stmt = $pdo->prepare(
            'UPDATE affinite
             SET type = CASE
                 WHEN valeur >= 70 THEN "ami"
                 WHEN valeur <= 30 THEN "rival"
                 ELSE "neutre"
             END
             WHERE id_partie = :partie AND id_personnage = :perso'
        );
        $stmt->execute([':partie' => $idPartie, ':perso' => $idPersonnage]);
    }

    // Vérifie si l'affinité avec un personnage est suffisante
    public static function verifieCondition(int $idPartie, int $idPersonnage, int $min): bool {
        $affinite = self::get($idPartie, $idPersonnage);
        if (!$affinite) return false;
        return (int)$affinite['valeur'] >= $min;
    }
}