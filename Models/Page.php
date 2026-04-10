<?php
 
require_once('Config/database.php');
 
class Page {
 
    // Récupère une page par son id
    public static function getById(int $id): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM page WHERE id_page = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
 
    // Récupère la page de départ (id = 1)
    public static function getAccueil(): array|false {
        return self::getById(1);
    }
 
    // Vérifie si une page est une fin
    public static function estFin(int $id): bool {
        $page = self::getById($id);
        return $page && (bool)$page['est_fin'];
    }
}