<?php
// controllers/ChoixController.php

require_once('Models/Choix.php');
require_once('Models/Partie.php');
require_once('Models/Stats.php');
require_once('Models/Inventaire.php');
require_once('Models/Objet.php');
require_once('Models/Historique.php');
require_once('Models/Page.php');
require_once('Models/Affinite.php');
require_once('Models/Journal.php');

class ChoixController {

    public static function traiter(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;
        if (!$idPartie) { header('Location: index.php?action=accueil'); exit; }

        $idChoix = (int)($_POST['id_choix'] ?? 0);
        if ($idChoix === 0) { header('Location: index.php?action=accueil'); exit; }

        $choix = Choix::getById($idChoix);
        if (!$choix) { header('Location: index.php?action=accueil'); exit; }

        $partie = Partie::getById($idPartie);
        if (!$partie || (int)$partie['page_actuelle'] !== (int)$choix['id_page_source']) {
            header('Location: index.php?action=page&id=' . ($partie['page_actuelle'] ?? 1)); exit;
        }

        $statsOk = Stats::verifieCondition(
            $idPartie,
            (int)$choix['cond_courage_min'],
            (int)$choix['cond_technique_min'],
            (int)$choix['cond_stamina_min'],
            (int)$choix['cond_vitesse_min'],
            (int)$choix['cond_chance_min'],
            (int)$choix['cond_leadership_min']
        );
        $objetOk = true;
        if (!empty($choix['cond_objet_requis'])) {
            $objetOk = Inventaire::possede($idPartie, (int)$choix['cond_objet_requis']);
        }
        $affiniteOk = true;
        if (!empty($choix['cond_affinite_perso'])) {
            $affiniteOk = Affinite::verifieCondition(
                $idPartie,
                (int)$choix['cond_affinite_perso'],
                (int)$choix['cond_affinite_min']
            );
        }

        if (!$statsOk || !$objetOk || !$affiniteOk) {
            header('Location: index.php?action=page&id=' . $partie['page_actuelle']); exit;
        }

        $idPageCible = (int)$choix['id_page_cible'];

        self::appliquerEffetsPage($idPartie, (int)$choix['id_page_source'], $idPageCible);

        Historique::enregistrer($idPartie, $idChoix);

        $pageCibleData = Page::getById($idPageCible);
        $estFin        = $pageCibleData && (bool)$pageCibleData['est_fin'];

        Stats::ajouterXP($idPartie, 10);
        Partie::majPage($idPartie, $idPageCible, $estFin);

        header('Location: index.php?action=page&id=' . $idPageCible); exit;
    }

    public static function repondreDialogue(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;
        if (!$idPartie) { header('Location: index.php?action=accueil'); exit; }

        $idReponse = (int)($_POST['id_reponse'] ?? 0);
        if ($idReponse === 0) { header('Location: index.php?action=accueil'); exit; }

        require_once('Models/Dialogue.php');
        
        $reponse = Dialogue::getReponseById($idReponse);
        if (!$reponse) { header('Location: index.php?action=accueil'); exit; }

        Dialogue::appliquerEffetsReponse($reponse, $idPartie);

        Stats::ajouterXP($idPartie, 5);

        $idPageSuivante = (int)($reponse['id_page_suivante'] ?? 0);
        if ($idPageSuivante === 0) {
            header('Location: index.php?action=accueil'); exit;
        }

        $pageSuivante = Page::getById($idPageSuivante);
        $estFin       = $pageSuivante && (bool)$pageSuivante['est_fin'];

        Partie::majPage($idPartie, $idPageSuivante, $estFin);
        header('Location: index.php?action=page&id=' . $idPageSuivante); exit;
    }

    // Effets narratifs — ne s'appliquent que si la page cible n'a pas encore été visitée
    private static function appliquerEffetsPage(int $idPartie, int $pageSource, int $pageCible): void {

        if (Journal::pageDejaVisitee($idPartie, $pageCible)) {
            return;
        }

        // Objets selon le chemin de préparation
        if ($pageSource === 11 && $pageCible === 12) {
            // Mark → Gants de Mark (objet 6)
            if (!Inventaire::possede($idPartie, 6)) {
                Inventaire::ajouter($idPartie, 6);
                Objet::appliquerEffets(6, $idPartie);
                $_SESSION['objet_notif'] = 6;
            }
            Affinite::modifier($idPartie, 1, 15);
        }
        if ($pageSource === 11 && $pageCible === 13) {
            // Jude → Carnet de Jude (objet 3)
            if (!Inventaire::possede($idPartie, 3)) {
                Inventaire::ajouter($idPartie, 3);
                Objet::appliquerEffets(3, $idPartie);
                $_SESSION['objet_notif'] = 3;
            }
            Affinite::modifier($idPartie, 3, 15);
        }
        if ($pageSource === 11 && $pageCible === 14) {
            // Nathan → Bracelet Nathan (objet 7)
            if (!Inventaire::possede($idPartie, 7)) {
                Inventaire::ajouter($idPartie, 7);
                Objet::appliquerEffets(7, $idPartie);
                $_SESSION['objet_notif'] = 7;
            }
            Affinite::modifier($idPartie, 4, 15);
        }
        if ($pageSource === 11 && $pageCible === 15) {
            // Solo → Stamina +1
            Stats::modifier($idPartie, 'stamina', 1);
            Affinite::modifier($idPartie, 5, 10);
        }

        // Affinités selon rencontre personnage
        if ($pageSource === 4 && $pageCible === 5)  Affinite::modifier($idPartie, 2, 5);
        if ($pageSource === 4 && $pageCible === 6)  Affinite::modifier($idPartie, 3, 5);
        if ($pageSource === 4 && $pageCible === 7)  Affinite::modifier($idPartie, 4, 5);
        if ($pageSource === 4 && $pageCible === 8)  Affinite::modifier($idPartie, 5, 5);

        // Après match entraînement → Courage +1
        if ($pageSource === 10 && $pageCible === 11) {
            Stats::modifier($idPartie, 'courage', 1);
        }

        // Après défaite → bandeau Mark si affinité suffisante
        if ($pageSource === 18 && $pageCible === 19) {
            if (!Inventaire::possede($idPartie, 1)) {
                $affMark = Affinite::get($idPartie, 1);
                if ($affMark && (int)$affMark['valeur'] >= 40) {
                    Inventaire::ajouter($idPartie, 1);
                    Objet::appliquerEffets(1, $idPartie);
                    $_SESSION['objet_notif'] = 1;
                }
            }
        }

        // Victoire finale Kantō → Boisson isotonique
        if ($pageSource === 30 && $pageCible === 31) {
            if (!Inventaire::possede($idPartie, 4)) {
                Inventaire::ajouter($idPartie, 4);
                Objet::appliquerEffets(4, $idPartie);
                $_SESSION['objet_notif'] = 4;
            }
            Affinite::modifier($idPartie, 1, 10);
            Affinite::modifier($idPartie, 2, 10);
        }

        // Victoire Kirkwood → Talisman de Raimon
        if ($pageSource === 40 && $pageCible === 41) {
            if (!Inventaire::possede($idPartie, 5)) {
                Inventaire::ajouter($idPartie, 5);
                Objet::appliquerEffets(5, $idPartie);
                $_SESSION['objet_notif'] = 5;
            }
            for ($i = 1; $i <= 5; $i++) {
                Affinite::modifier($idPartie, $i, 8);
            }
        }
    }
}