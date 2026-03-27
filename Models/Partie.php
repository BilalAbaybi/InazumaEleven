<?php

require_once '../InazumaEleven/Config/database.php';

class Partie {

    // Crée une nouvelle partie et retourne son id
    public static function creer(string $pseudo): int {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'INSERT INTO partie (pseudo, page_actuelle, date_debut)
             VALUES (:pseudo, 1, NOW())'
        );
        $stmt->execute([':pseudo' => $pseudo]);
        return (int)$pdo->lastInsertId();
    }

    // Récupère une partie par son id
    public static function getById(int $id): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM partie WHERE id_partie = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Met à jour la page actuelle du joueur
    public static function majPage(int $idPartie, int $idPage): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'UPDATE partie
             SET page_actuelle  = :page,
                 nb_pages_vues  = nb_pages_vues + 1
             WHERE id_partie = :id'
        );
        $stmt->execute([':page' => $idPage, ':id' => $idPartie]);
    }

    // Termine une partie avec le type de fin obtenu
    public static function terminer(int $idPartie, string $typeFin): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'UPDATE partie
             SET terminee     = TRUE,
                 fin_obtenue  = :fin
             WHERE id_partie = :id'
        );
        $stmt->execute([':fin' => $typeFin, ':id' => $idPartie]);
    }
}