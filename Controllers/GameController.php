<?php
// controllers/GameController.php

require_once('Models/Partie.php');
require_once('Models/Stats.php');
require_once('Models/Page.php');
require_once('Models/Inventaire.php');
require_once('Models/Historique.php');
require_once('Models/Joueur.php');
require_once('Models/Affinite.php');
require_once('Models/Journal.php');
require_once('Models/MatchSimule.php');

class GameController {

    public static function accueil(): void {
        require 'Views/accueil.php';
    }

    public static function nouvellePartie(): void {
        $pseudo     = trim($_POST['pseudo']       ?? '');
        $motDePasse = trim($_POST['mot_de_passe'] ?? '');

        if ($pseudo === '' || strlen($pseudo) > 50) {
            $_SESSION['erreur'] = 'Pseudo invalide (1-50 caractères).';
            header('Location: index.php?action=accueil'); exit;
        }
        if (strlen($motDePasse) < 4) {
            $_SESSION['erreur'] = 'Mot de passe trop court (4 caractères minimum).';
            header('Location: index.php?action=accueil'); exit;
        }

        $idJoueur = Joueur::creer($pseudo, $motDePasse);
        if ($idJoueur === false) {
            $_SESSION['erreur'] = 'Ce pseudo est déjà utilisé. Connecte-toi ou choisis un autre pseudo.';
            header('Location: index.php?action=accueil'); exit;
        }

        $idPartie = Partie::creer($pseudo, $idJoueur);
        Stats::creer($idPartie);

        $_SESSION['id_partie'] = $idPartie;
        $_SESSION['id_joueur'] = $idJoueur;
        $_SESSION['pseudo']    = $pseudo;

        // Nettoyer toute trace de matchs précédents
        unset($_SESSION['match_fin'], $_SESSION['event_aleatoire'], $_SESSION['objet_notif'],
              $_SESSION['match_resultat_action'], $_SESSION['niveau_up'], $_SESSION['succes']);

        header('Location: index.php?action=page&id=1'); exit;
    }

    public static function continuer(): void {
        $pseudo     = trim($_POST['pseudo']       ?? '');
        $motDePasse = trim($_POST['mot_de_passe'] ?? '');

        $joueur = Joueur::connecter($pseudo, $motDePasse);
        if (!$joueur) {
            $_SESSION['erreur'] = 'Pseudo ou mot de passe incorrect.';
            header('Location: index.php?action=accueil'); exit;
        }

        $partie = Joueur::getPartiEnCours((int)$joueur['id_joueur']);

        $_SESSION['id_joueur'] = (int)$joueur['id_joueur'];
        $_SESSION['pseudo']    = $joueur['pseudo'];

        if (!$partie) {
            $idPartie = Partie::creer($joueur['pseudo'], (int)$joueur['id_joueur']);
            Stats::creer($idPartie);
            $_SESSION['id_partie'] = $idPartie;
            header('Location: index.php?action=page&id=1'); exit;
        }

        $_SESSION['id_partie'] = (int)$partie['id_partie'];
        header('Location: index.php?action=page&id=' . (int)$partie['page_actuelle']); exit;
    }

    public static function sauvegarder(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;
        if (!$idPartie) { header('Location: index.php?action=accueil'); exit; }

        $partie = Partie::getById($idPartie);
        $_SESSION['succes'] = 'Partie sauvegardée à la page ' . $partie['page_actuelle'] . ' !';
        header('Location: index.php?action=page&id=' . $partie['page_actuelle']); exit;
    }

    public static function recommencer(): void {
        $idJoueur = $_SESSION['id_joueur'] ?? null;
        $pseudo   = $_SESSION['pseudo']    ?? null;
        if (!$idJoueur || !$pseudo) { header('Location: index.php?action=accueil'); exit; }

        $anciennePartie = Joueur::getPartiEnCours((int)$idJoueur);
        if ($anciennePartie) {
            Partie::terminer((int)$anciennePartie['id_partie'], 'abandonnee');
        }

        $idPartie = Partie::creer($pseudo, (int)$idJoueur);
        Stats::creer($idPartie);
        $_SESSION['id_partie'] = $idPartie;

        // Nettoyer toute trace de matchs précédents
        unset($_SESSION['match_fin'], $_SESSION['event_aleatoire'], $_SESSION['objet_notif'],
              $_SESSION['match_resultat_action'], $_SESSION['niveau_up'], $_SESSION['succes']);

        header('Location: index.php?action=page&id=1'); exit;
    }

    public static function fin(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;
        if (!$idPartie) { header('Location: index.php?action=accueil'); exit; }

        $partie     = Partie::getById($idPartie);
        $stats      = Stats::getByPartie($idPartie);
        $inventaire = Inventaire::getByPartie($idPartie);
        $historique = Historique::getByPartie($idPartie);
        $affinites  = Affinite::getAll($idPartie);
        $journal    = Journal::getByPartie($idPartie);
        $matchs     = MatchSimule::getHistorique($idPartie);

        $imgFin = null;
        if ($partie) {
            $pageFin = Page::getById((int)$partie['page_actuelle']);
            if ($pageFin && !empty($pageFin['image'])) {
                $imgFin = $pageFin['image'];
            }
        }

        require 'Views/fin.php';
    }

    public static function journal(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;
        if (!$idPartie) { header('Location: index.php?action=accueil'); exit; }

        $journal = Journal::getByPartie($idPartie);
        $stats   = Stats::getByPartie($idPartie);
        require 'Views/journal.php';
    }
}