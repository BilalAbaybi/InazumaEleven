<?php

require_once '../InazumaEleven/Config/database.php';

class Inventaire {

    // Ajoute un objet à l'inventaire du joueur
    public static function ajouter(int $idPartie, int $idObjet): void {
        // On n'ajoute pas deux fois le même objet
        if (self::possede($idPartie, $idObjet)) return;

        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'INSERT INTO inventaire_joueur (id_partie, id_objet, date_obtention)
             VALUES (:partie, :objet, NOW())'
        );
        $stmt->execute([':partie' => $idPartie, ':objet' => $idObjet]);
    }

    // Vérifie si le joueur possède un objet
    public static function possede(int $idPartie, int $idObjet): bool {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM inventaire_joueur
             WHERE id_partie = :partie AND id_objet = :objet'
        );
        $stmt->execute([':partie' => $idPartie, ':objet' => $idObjet]);
        return (int)$stmt->fetchColumn() > 0;
    }

    // Récupère tous les objets de l'inventaire avec leurs infos
    public static function getByPartie(int $idPartie): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT o.*
             FROM inventaire_joueur ij
             JOIN objet o ON o.id_objet = ij.id_objet
             WHERE ij.id_partie = :id
             ORDER BY ij.date_obtention ASC'
        );
        $stmt->execute([':id' => $idPartie]);
        return $stmt->fetchAll();
    }
}