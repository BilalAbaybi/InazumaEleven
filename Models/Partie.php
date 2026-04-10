<?php
// models/Partie.php

require_once('Config/database.php');

class Partie {

    public static function creer(string $pseudo, int $idJoueur): int {
        $pdo  = getDB();
        $pdo->prepare(
            'INSERT INTO partie (id_joueur, pseudo, page_actuelle, date_debut, nb_pages_vues)
             VALUES (:joueur, :pseudo, 1, NOW(), 1)'
        )->execute([':joueur' => $idJoueur, ':pseudo' => $pseudo]);

        $idPartie = (int)$pdo->lastInsertId();

        // Initialiser les affinités avec tous les personnages
        require_once __DIR__ . '/../Models/Affinite.php';
        Affinite::initialiser($idPartie);

        return $idPartie;
    }

    public static function getById(int $id): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM partie WHERE id_partie = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function majPage(int $idPartie, int $idPage, bool $estFin = false): void {
        $pdo = getDB();
        if ($estFin) {
            $pdo->prepare('UPDATE partie SET page_actuelle = :page WHERE id_partie = :id')
                ->execute([':page' => $idPage, ':id' => $idPartie]);
        } else {
            $pdo->prepare(
                'UPDATE partie
                 SET page_actuelle = :page, nb_pages_vues = nb_pages_vues + 1
                 WHERE id_partie = :id'
            )->execute([':page' => $idPage, ':id' => $idPartie]);
        }
    }

    public static function terminer(int $idPartie, string $typeFin): void {
        $pdo  = getDB();
        $pdo->prepare(
            'UPDATE partie SET terminee = TRUE, fin_obtenue = :fin WHERE id_partie = :id'
        )->execute([':fin' => $typeFin, ':id' => $idPartie]);
    }
}