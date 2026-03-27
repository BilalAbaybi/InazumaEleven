<?php

require_once '../InazumaEleven/Config/database.php';

class Objet {

    // Récupère un objet par son id
    public static function getById(int $id): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM objet WHERE id_objet = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Récupère tous les objets (utile pour l'admin ou debug)
    public static function getAll(): array {
        $pdo  = getDB();
        $stmt = $pdo->query('SELECT * FROM objet');
        return $stmt->fetchAll();
    }

    // Applique les effets d'un objet sur les stats du joueur
    // appelé automatiquement quand le joueur ramasse un objet
    public static function appliquerEffets(int $idObjet, int $idPartie): void {
        require_once __DIR__ . '/Stats.php';

        $objet = self::getById($idObjet);
        if (!$objet) return;

        // On applique chaque effet seulement s'il est non nul
        if ((int)$objet['effet_courage'] !== 0) {
            Stats::modifier($idPartie, 'courage', (int)$objet['effet_courage']);
        }
        if ((int)$objet['effet_technique'] !== 0) {
            Stats::modifier($idPartie, 'technique', (int)$objet['effet_technique']);
        }
        if ((int)$objet['effet_stamina'] !== 0) {
            Stats::modifier($idPartie, 'stamina', (int)$objet['effet_stamina']);
        }
    }
}