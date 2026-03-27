<?php

require_once '../InazumaEleven/Config/database.php';
require_once '../InazumaEleven/Models/Stats.php';
require_once '../InazumaEleven/Models/Inventaire.php';

class Choix {

    // Récupère un choix par son id
    public static function getById(int $id): array|false {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT * FROM choix WHERE id_choix = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Récupère TOUS les choix d'une page source
    public static function getByPage(int $idPage): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            'SELECT * FROM choix WHERE id_page_source = :id'
        );
        $stmt->execute([':id' => $idPage]);
        return $stmt->fetchAll();
    }

    // Récupère uniquement les choix disponibles pour le joueur
    // (filtre selon ses stats et son inventaire)
    public static function getDisponibles(int $idPage, int $idPartie): array {
        $tousLesChoix = self::getByPage($idPage);
        $choixDispo   = [];

        foreach ($tousLesChoix as $choix) {
            // Vérification des stats minimales
            $statsOk = Stats::verifieCondition(
                $idPartie,
                (int)$choix['cond_courage_min'],
                (int)$choix['cond_technique_min'],
                (int)$choix['cond_stamina_min']
            );

            // Vérification de l'objet requis (si applicable)
            $objetOk = true;
            if (!empty($choix['cond_objet_requis'])) {
                $objetOk = Inventaire::possede(
                    $idPartie,
                    (int)$choix['cond_objet_requis']
                );
            }

            if ($statsOk && $objetOk) {
                $choixDispo[] = $choix;
            }
        }

        return $choixDispo;
    }
}