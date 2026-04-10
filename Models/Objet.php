<?php
// models/Objet.php

require_once('Config/database.php');

class Objet {

    public static function getById(int $id): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM objet WHERE id_objet = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function getAll(): array {
        $pdo  = getDB();
        return $pdo->query('SELECT * FROM objet')->fetchAll();
    }

    // Applique tous les effets d'un objet (6 stats v2)
    public static function appliquerEffets(int $idObjet, int $idPartie): void {
        $objet = self::getById($idObjet);
        if (!$objet) return;

        $effets = [
            'courage'    => (int)($objet['effet_courage']    ?? 0),
            'technique'  => (int)($objet['effet_technique']  ?? 0),
            'stamina'    => (int)($objet['effet_stamina']     ?? 0),
            'vitesse'    => (int)($objet['effet_vitesse']     ?? 0),
            'chance'     => (int)($objet['effet_chance']      ?? 0),
            'leadership' => (int)($objet['effet_leadership']  ?? 0),
        ];

        foreach ($effets as $stat => $delta) {
            if ($delta !== 0) {
                Stats::modifier($idPartie, $stat, $delta);
            }
        }
    }
}