<?php
// models/Journal.php

require_once('Config/database.php');

class Journal {

    public static function ajouter(int $idPartie, int $idPage, string $resume): void {
        if (self::pageDejaEnregistree($idPartie, $idPage)) return;

        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'INSERT INTO journal (id_partie, id_page, resume, date_visite)
             VALUES (:partie, :page, :resume, NOW())'
        );
        $stmt->execute([
            ':partie' => $idPartie,
            ':page'   => $idPage,
            ':resume' => $resume,
        ]);
    }

    public static function getByPartie(int $idPartie): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT j.*, p.titre, p.image, p.type_page
             FROM journal j
             JOIN page p ON p.id_page = j.id_page
             WHERE j.id_partie = :id
             ORDER BY j.date_visite ASC'
        );
        $stmt->execute([':id' => $idPartie]);
        return $stmt->fetchAll();
    }

    public static function compter(int $idPartie): int {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM journal WHERE id_partie = :id');
        $stmt->execute([':id' => $idPartie]);
        return (int)$stmt->fetchColumn();
    }

    // Méthode publique utilisée par Dialogue et ChoixController
    public static function pageDejaVisitee(int $idPartie, int $idPage): bool {
        return self::pageDejaEnregistree($idPartie, $idPage);
    }

    private static function pageDejaEnregistree(int $idPartie, int $idPage): bool {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM journal WHERE id_partie = :partie AND id_page = :page'
        );
        $stmt->execute([':partie' => $idPartie, ':page' => $idPage]);
        return (int)$stmt->fetchColumn() > 0;
    }
}