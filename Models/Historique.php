<?php
// models/Historique.php

require_once('Config/database.php');

class Historique {

    // Enregistre un choix effectué par le joueur
    public static function enregistrer(int $idPartie, int $idChoix): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'INSERT INTO historique_choix (id_partie, id_choix, date_choix)
             VALUES (:partie, :choix, NOW())'
        );
        $stmt->execute([':partie' => $idPartie, ':choix' => $idChoix]);
    }

    // Récupère tout l'historique d'une partie (pour les stats de fin)
    public static function getByPartie(int $idPartie): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT h.*, c.texte_bouton, c.id_page_source, c.id_page_cible
             FROM historique_choix h
             JOIN choix c ON c.id_choix = h.id_choix
             WHERE h.id_partie = :id
             ORDER BY h.date_choix ASC'
        );
        $stmt->execute([':id' => $idPartie]);
        return $stmt->fetchAll();
    }

    // Vérifie si une page a déjà été visitée (anti retour arrière)
    public static function pageDejaVue(int $idPartie, int $idPage): bool {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*)
             FROM historique_choix h
             JOIN choix c ON c.id_choix = h.id_choix
             WHERE h.id_partie = :partie
               AND c.id_page_cible = :page'
        );
        $stmt->execute([':partie' => $idPartie, ':page' => $idPage]);
        return (int)$stmt->fetchColumn() > 0;
    }
}