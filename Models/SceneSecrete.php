<?php
// models/SceneSecrete.php

require_once('Config/database.php');

class SceneSecrete {

    // Vérifie si une scène secrète se déclenche sur cette page
    // Retourne la page secrète si conditions remplies, false sinon
    public static function verifier(int $idPage, int $idPartie): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT * FROM scene_secrete
             WHERE id_page = :page AND decouverte = FALSE'
        );
        $stmt->execute([':page' => $idPage]);
        $scenes = $stmt->fetchAll();

        foreach ($scenes as $scene) {
            if (self::verifieConditions($scene['condition_json'], $idPartie)) {
                // Marquer comme découverte
                $pdo->prepare('UPDATE scene_secrete SET decouverte = TRUE WHERE id_scene = :id')
                    ->execute([':id' => $scene['id_scene']]);

                // Retourner la page secrète
                require_once __DIR__ . '/Page.php';
                return Page::getById((int)$scene['id_page_secrete']);
            }
        }
        return false;
    }

    // Vérifie les conditions JSON d'une scène secrète
    // Ex: {"courage_min":3, "affinite_1":80, "objet_requis":1}
    private static function verifieConditions(string $conditionJson, int $idPartie): bool {
        $cond = json_decode($conditionJson, true);
        if (!$cond) return false;

        require_once __DIR__ . '/Stats.php';
        require_once __DIR__ . '/Affinite.php';
        require_once __DIR__ . '/Inventaire.php';

        $stats = Stats::getByPartie($idPartie);

        foreach ($cond as $cle => $valeur) {
            // Stats minimales
            if (str_ends_with($cle, '_min')) {
                $stat = str_replace('_min', '', $cle);
                if (isset($stats[$stat]) && $stats[$stat] < $valeur) return false;
            }
            // Affinité minimale avec un personnage
            if (str_starts_with($cle, 'affinite_')) {
                $idPerso = (int)str_replace('affinite_', '', $cle);
                if (!Affinite::verifieCondition($idPartie, $idPerso, $valeur)) return false;
            }
            // Objet requis dans l'inventaire
            if ($cle === 'objet_requis') {
                if (!Inventaire::possede($idPartie, (int)$valeur)) return false;
            }
            // Niveau minimum
            if ($cle === 'niveau_min' && $stats['niveau'] < $valeur) return false;
        }
        return true;
    }
}


// ============================================================
// EvenementAleatoire — dans le même fichier pour simplifier
// ============================================================

class EvenementAleatoire {

    // Vérifie et déclenche un événement aléatoire sur une page
    // Retourne ['declenche' => bool, 'texte' => string, 'effets' => array]
    public static function tenter(int $idPage, int $idPartie): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT * FROM evenement_aleatoire WHERE id_page = :id'
        );
        $stmt->execute([':id' => $idPage]);
        $events = $stmt->fetchAll();

        if (empty($events)) return ['declenche' => false, 'texte' => '', 'effets' => []];

        require_once __DIR__ . '/Stats.php';
        $stats = Stats::getByPartie($idPartie);
        $chance = $stats['chance'] ?? 1;

        foreach ($events as $event) {
            // Probabilité modifiée par la stat chance
            // chance 1 = proba normale, chance 5 = proba +20%
            $probaFinale = min(95, $event['proba_base'] + ($chance - 1) * 5);
            $tirage      = rand(1, 100);

            if ($tirage <= $probaFinale) {
                // Appliquer les effets JSON
                $effets = json_decode($event['effet_json'], true) ?? [];
                if (!empty($effets)) {
                    Stats::appliquerJSON($idPartie, $event['effet_json']);
                }

                return [
                    'declenche' => true,
                    'texte'     => $event['texte_declenchement'],
                    'effets'    => $effets,
                ];
            }
        }

        return ['declenche' => false, 'texte' => '', 'effets' => []];
    }
}