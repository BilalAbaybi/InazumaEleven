<?php
// models/Joueur.php

require_once('Config/database.php');

class Joueur {

    // Crée un nouveau compte joueur
    // Retourne l'id_joueur créé, ou false si le pseudo est déjà pris
    public static function creer(string $pseudo, string $motDePasse): int|false {
        // Vérif que le pseudo n'existe pas déjà
        if (self::getByPseudo($pseudo)) {
            return false;
        }

        $pdo  = getDB();
        $hash = password_hash($motDePasse, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO joueur (pseudo, mot_de_passe, date_creation)
             VALUES (:pseudo, :mdp, NOW())'
        );
        $stmt->execute([':pseudo' => $pseudo, ':mdp' => $hash]);
        return (int)$pdo->lastInsertId();
    }

    // Récupère un joueur par son pseudo
    public static function getByPseudo(string $pseudo): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT * FROM joueur WHERE pseudo = :pseudo'
        );
        $stmt->execute([':pseudo' => $pseudo]);
        return $stmt->fetch();
    }

    // Récupère un joueur par son id
    public static function getById(int $id): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT * FROM joueur WHERE id_joueur = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Vérifie le mot de passe d'un joueur
    // Retourne le joueur si correct, false sinon
    public static function connecter(string $pseudo, string $motDePasse): array|false {
        $joueur = self::getByPseudo($pseudo);
        if (!$joueur) return false;

        if (!password_verify($motDePasse, $joueur['mot_de_passe'])) {
            return false;
        }

        return $joueur;
    }

    // Récupère la dernière partie en cours d'un joueur (non terminée)
    public static function getPartiEnCours(int $idJoueur): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT * FROM partie
             WHERE id_joueur = :id
               AND terminee  = FALSE
             ORDER BY date_debut DESC
             LIMIT 1'
        );
        $stmt->execute([':id' => $idJoueur]);
        return $stmt->fetch();
    }
}