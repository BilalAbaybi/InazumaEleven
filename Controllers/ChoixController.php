<?php

require_once '../InazumaEleven/Models/Choix.php';
require_once '../InazumaEleven/Models/Partie.php';
require_once '../InazumaEleven/Models/Stats.php';
require_once '../InazumaEleven/Models/Inventaire.php';
require_once '../InazumaEleven/Models/Objet.php';
require_once '../InazumaEleven/Models/Historique.php';
require_once '../InazumaEleven/Models/Page.php';

class ChoixController {

    /**
     * Traite le choix sélectionné par le joueur
     * POST /index.php?action=choisir
     */
    public static function traiter(): void {
        // Vérification de session
        $idPartie = $_SESSION['id_partie'] ?? null;

        if (!$idPartie) {
            header('Location: index.php?action=accueil');
            exit;
        }

        // Récupération du choix soumis via POST
        $idChoix = (int)($_POST['id_choix'] ?? 0);

        if ($idChoix === 0) {
            header('Location: index.php?action=accueil');
            exit;
        }

        // Récupération du choix en BDD
        $choix = Choix::getById($idChoix);
        if (!$choix) {
            header('Location: index.php?action=accueil');
            exit;
        }

        // Vérification que ce choix correspond bien à la page actuelle du joueur
        // (sécurité anti-triche : on ne peut pas soumettre un choix d'une autre page)
        $partie = Partie::getById($idPartie);
        if (!$partie || (int)$partie['page_actuelle'] !== (int)$choix['id_page_source']) {
            header('Location: index.php?action=page&id=' . ($partie['page_actuelle'] ?? 1));
            exit;
        }

        // Vérification que le joueur remplit les conditions du choix
        $statsOk = Stats::verifieCondition(
            $idPartie,
            (int)$choix['cond_courage_min'],
            (int)$choix['cond_technique_min'],
            (int)$choix['cond_stamina_min']
        );

        $objetOk = true;
        if (!empty($choix['cond_objet_requis'])) {
            $objetOk = Inventaire::possede($idPartie, (int)$choix['cond_objet_requis']);
        }

        if (!$statsOk || !$objetOk) {
            // Le joueur ne remplit pas les conditions (triche ou bug) : on ne fait rien
            header('Location: index.php?action=page&id=' . $partie['page_actuelle']);
            exit;
        }

        // --- Effets spéciaux selon la page cible ---
        // Ces effets sont déclenchés APRÈS la validation du choix
        $idPageCible = (int)$choix['id_page_cible'];
        self::appliquerEffetsPage($idPartie, (int)$choix['id_page_source'], $idPageCible);

        // Enregistrement du choix dans l'historique
        Historique::enregistrer($idPartie, $idChoix);

        // Mise à jour de la page actuelle en BDD
        Partie::majPage($idPartie, $idPageCible);

        // Redirection vers la nouvelle page
        header('Location: index.php?action=page&id=' . $idPageCible);
        exit;
    }

    /**
     * Applique les effets de stats et d'inventaire selon les choix de l'histoire.
     * Cette méthode centralise toute la logique narrative :
     *   - Ramasser un objet donne des stats
     *   - Certains chemins coûtent de la stamina
     */
    private static function appliquerEffetsPage(int $idPartie, int $pageSource, int $pageCible): void {
        // Page 3A → 4A (prendre le bandeau) : +1 courage via objet bandeau (id=1)
        if ($pageSource === 3 && $pageCible === 5) {
            Inventaire::ajouter($idPartie, 1); // bandeau de Mark
            Objet::appliquerEffets(1, $idPartie);
        }

        // Page 3A → 4B (prendre les crampons) : +1 technique via objet crampons (id=2)
        if ($pageSource === 3 && $pageCible === 6) {
            Inventaire::ajouter($idPartie, 2); // crampons d'Axel
            Objet::appliquerEffets(2, $idPartie);
        }

        // Page 3B → 4C (prendre le carnet) : +1 technique via objet carnet (id=3)
        if ($pageSource === 4 && $pageCible === 7) {
            Inventaire::ajouter($idPartie, 3); // carnet de Jude Sharp
            Objet::appliquerEffets(3, $idPartie);
        }

        // Page 3B (solo) : stamina -1
        if ($pageSource === 2 && $pageCible === 4) {
            Stats::modifier($idPartie, 'stamina', -1);
        }

        // Page 4D (rien pris) : stamina -1 supplémentaire
        if ($pageSource === 4 && $pageCible === 8) {
            Stats::modifier($idPartie, 'stamina', -1);
        }

        // Page 6B (jeu collectif) : +1 courage
        if ($pageSource === 9 && $pageCible === 12) {
            Stats::modifier($idPartie, 'courage', 1);
        }

        // Page 6A sans crampons (rebond Kevin) : stamina -1
        // Page 6C (sacrifice) : stamina -1
        if ($pageSource === 9 && $pageCible === 13) {
            Stats::modifier($idPartie, 'stamina', -1);
        }
    }
}