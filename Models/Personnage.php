<?php
// models/Personnage.php

require_once('Config/database.php');

class Personnage {

    public static function getById(int $id): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM personnage WHERE id_personnage = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function getAll(): array {
        $pdo  = getDB();
        return $pdo->query('SELECT * FROM personnage ORDER BY id_personnage')->fetchAll();
    }

    public static function getByEquipe(string $equipe): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM personnage WHERE equipe = :equipe');
        $stmt->execute([':equipe' => $equipe]);
        return $stmt->fetchAll();
    }

    // Retourne les stats de combat sous forme de tableau PHP
    public static function getStatsCombat(int $id): array {
        $perso = self::getById($id);
        if (!$perso || empty($perso['stats_combat'])) return [];
        return json_decode($perso['stats_combat'], true) ?? [];
    }
}