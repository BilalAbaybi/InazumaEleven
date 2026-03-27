<?php

require_once '../InazumaEleven/Models/Page.php';
require_once '../InazumaEleven/Models/Choix.php';
require_once '../InazumaEleven/Models/Stats.php';
require_once '../InazumaEleven/Models/Inventaire.php';
require_once '../InazumaEleven/Models/Historique.php';
require_once '../InazumaEleven/Models/Partie.php';

class PageController {

    /**
     * Affiche une page de l'histoire avec ses choix disponibles
     * GET /index.php?action=page&id=X
     */
    public static function afficher(): void {
        // Vérification de session
        $idPartie = $_SESSION['id_partie'] ?? null;
        $pseudo   = $_SESSION['pseudo']    ?? 'Joueur';

        if (!$idPartie) {
            header('Location: index.php?action=accueil');
            exit;
        }

        // Récupération de l'id de page demandé
        $idPage = (int)($_GET['id'] ?? 1);

        // Anti-retour arrière : si la page a déjà été visitée via l'historique
        // ET que ce n'est pas la page actuelle, on bloque
        $partie = Partie::getById($idPartie);
        if (!$partie) {
            header('Location: index.php?action=accueil');
            exit;
        }

        // Blocage du bouton précédent : on vérifie que la page demandée
        // correspond bien à la page actuelle en BDD
        if ((int)$partie['page_actuelle'] !== $idPage) {
            // Le joueur a tenté de revenir en arrière : on le renvoie sur sa page courante
            header('Location: index.php?action=page&id=' . $partie['page_actuelle']);
            exit;
        }

        // Récupération de la page
        $page = Page::getById($idPage);
        if (!$page) {
            header('Location: index.php?action=accueil');
            exit;
        }

        // Personnalisation du texte avec le pseudo du joueur
        $page['texte'] = str_replace('Takamura', htmlspecialchars($pseudo), $page['texte']);
        $page['titre'] = str_replace('Takamura', htmlspecialchars($pseudo), $page['titre']);

        // Si c'est une page de fin, on termine la partie et on redirige
        if ($page['est_fin']) {
            Partie::terminer($idPartie, $page['type_fin'] ?? 'defaite');
            header('Location: index.php?action=fin');
            exit;
        }

        // Récupération des choix disponibles selon les stats et l'inventaire
        $choixDisponibles = Choix::getDisponibles($idPage, $idPartie);

        // Stats et inventaire pour l'affichage dans la sidebar
        $stats      = Stats::getByPartie($idPartie);
        $inventaire = Inventaire::getByPartie($idPartie);

        require '../InazumaEleven/Views/page.php';
    }
}