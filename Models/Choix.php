<?php
// models/Choix.php

require_once('Config/database.php');
require_once('Models/Stats.php');
require_once('Models/Inventaire.php');
require_once('Models/Affinite.php');

class Choix {

    public static function getById(int $id): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM choix WHERE id_choix = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public static function getByPage(int $idPage): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM choix WHERE id_page_source = :id');
        $stmt->execute([':id' => $idPage]);
        return $stmt->fetchAll();
    }

    // Récupère les choix disponibles selon toutes les conditions v2
    public static function getDisponibles(int $idPage, int $idPartie): array {
        $tousLesChoix  = self::getByPage($idPage);
        $choixDispo    = [];
        $textesDejaVus = [];

        foreach ($tousLesChoix as $choix) {
            // Vérification stats complètes v2
            $statsOk = Stats::verifieCondition(
                $idPartie,
                (int)$choix['cond_courage_min'],
                (int)$choix['cond_technique_min'],
                (int)$choix['cond_stamina_min'],
                (int)$choix['cond_vitesse_min'],
                (int)$choix['cond_chance_min'],
                (int)$choix['cond_leadership_min']
            );

            // Vérification objet requis
            $objetOk = true;
            if (!empty($choix['cond_objet_requis'])) {
                $objetOk = Inventaire::possede($idPartie, (int)$choix['cond_objet_requis']);
            }

            // Vérification affinité requise
            $affiniteOk = true;
            if (!empty($choix['cond_affinite_perso'])) {
                $affiniteOk = Affinite::verifieCondition(
                    $idPartie,
                    (int)$choix['cond_affinite_perso'],
                    (int)$choix['cond_affinite_min']
                );
            }

            if ($statsOk && $objetOk && $affiniteOk) {
                // Anti-doublon par texte + destination
                $cle = $choix['texte_bouton'] . '|' . $choix['id_page_cible'];
                if (!in_array($cle, $textesDejaVus)) {
                    $textesDejaVus[] = $cle;
                    $choixDispo[]    = $choix;
                }
            }
        }
        return $choixDispo;
    }
}