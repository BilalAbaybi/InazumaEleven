<?php

require_once '../InazumaEleven/Models/Partie.php';
require_once '../InazumaEleven/Models/Stats.php';

class GameController {

    /**
     * Affiche le formulaire d'accueil (saisie du pseudo + intro)
     * GET /index.php?action=accueil
     */
    public static function accueil(): void {
        require '../InazumaEleven/Views/accueil.php';
    }

    /**
     * Crée une nouvelle partie et redirige vers la page 1
     * POST /index.php?action=nouvellePartie
     */
    public static function nouvellePartie(): void {
        // Validation du pseudo
        $pseudo = trim($_POST['pseudo'] ?? '');

        if ($pseudo === '' || strlen($pseudo) > 50) {
            $_SESSION['erreur'] = 'Merci d\'entrer un pseudo valide (1-50 caractères).';
            header('Location: index.php?action=accueil');
            exit;
        }

        // Création de la partie en BDD
        $idPartie = Partie::creer($pseudo);

        // Création des stats de départ (courage=1, technique=1, stamina=2)
        Stats::creer($idPartie);

        // On stocke l'id de partie en session
        $_SESSION['id_partie'] = $idPartie;
        $_SESSION['pseudo']    = $pseudo;

        // Redirection vers la page 1
        header('Location: index.php?action=page&id=1');
        exit;
    }

    /**
     * Affiche la page de fin avec les stats de la partie
     * GET /index.php?action=fin
     */
    public static function fin(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;

        if (!$idPartie) {
            header('Location: index.php?action=accueil');
            exit;
        }

        require_once '../InazumaEleven/Models/Stats.php';
        require_once '../InazumaEleven/Models/Inventaire.php';
        require_once '../InazumaEleven/Models/Historique.php';

        $partie    = Partie::getById($idPartie);
        $stats     = Stats::getByPartie($idPartie);
        $inventaire = Inventaire::getByPartie($idPartie);
        $historique = Historique::getByPartie($idPartie);

        require '../InazumaEleven/Views/fin.php';
    }
}