<?php
// models/Dialogue.php

require_once('Config/database.php');

class Dialogue {

    public static function getByPage(int $idPage): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT d.*, p.nom AS perso_nom, p.image AS perso_image, p.poste AS perso_poste
             FROM dialogue d
             JOIN personnage p ON p.id_personnage = d.id_personnage
             WHERE d.id_page = :id
             ORDER BY d.ordre ASC'
        );
        $stmt->execute([':id' => $idPage]);
        return $stmt->fetchAll();
    }

    public static function getReponses(int $idDialogue, int $idPartie): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT * FROM reponse_dialogue
             WHERE id_dialogue = :id
             ORDER BY ordre ASC'
        );
        $stmt->execute([':id' => $idDialogue]);
        $toutes = $stmt->fetchAll();

        $disponibles = [];
        foreach ($toutes as $reponse) {
            if (self::verifieConditionReponse($reponse, $idPartie)) {
                $disponibles[] = $reponse;
            }
        }
        return $disponibles;
    }

    public static function getReponseById(int $id): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM reponse_dialogue WHERE id_reponse = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    private static function verifieConditionReponse(array $reponse, int $idPartie): bool {
        if (empty($reponse['condition_json'])) return true;

        require_once __DIR__ . '/Stats.php';
        require_once __DIR__ . '/Affinite.php';

        $cond = json_decode($reponse['condition_json'], true);
        if (!$cond) return true;

        $stats = Stats::getByPartie($idPartie);

        foreach ($cond as $cle => $valeur) {
            if (str_ends_with($cle, '_min')) {
                $stat = str_replace('_min', '', $cle);
                if (isset($stats[$stat]) && $stats[$stat] < $valeur) return false;
            }
            if (str_starts_with($cle, 'affinite_')) {
                $idPerso = (int)str_replace('affinite_', '', $cle);
                if (!Affinite::verifieCondition($idPartie, $idPerso, $valeur)) return false;
            }
        }
        return true;
    }

    // Applique les effets d'une réponse — protégé contre les doublons via Journal
    public static function appliquerEffetsReponse(array $reponse, int $idPartie): void {
        require_once __DIR__ . '/Stats.php';
        require_once __DIR__ . '/Affinite.php';
        require_once __DIR__ . '/Journal.php';

        // Si la page cible a déjà été visitée, ne pas ré-appliquer
        $idPageCible = (int)($reponse['id_page_suivante'] ?? 0);
        if ($idPageCible > 0 && Journal::pageDejaVisitee($idPartie, $idPageCible)) {
            return;
        }

        if (!empty($reponse['effet_stats'])) {
            Stats::appliquerJSON($idPartie, $reponse['effet_stats']);
        }

        if (!empty($reponse['effet_affinite']) && (int)$reponse['effet_affinite'] !== 0) {
            $pdo  = getDB();
            $stmt = $pdo->prepare('SELECT id_personnage FROM dialogue WHERE id_dialogue = :id');
            $stmt->execute([':id' => $reponse['id_dialogue']]);
            $dialogue = $stmt->fetch();
            if ($dialogue) {
                Affinite::modifier($idPartie, (int)$dialogue['id_personnage'], (int)$reponse['effet_affinite']);
            }
        }
    }
}