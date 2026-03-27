<?php

require_once '../InazumaEleven/Models/Page.php';
require_once '../InazumaEleven/Models/Choix.php';
require_once '../InazumaEleven/Models/Stats.php';
require_once '../InazumaEleven/Models/Inventaire.php';
require_once '../InazumaEleven/Models/Historique.php';
require_once '../InazumaEleven/Models/Partie.php';

class PageController {

    public static function afficher(): void {
        $idPartie = $_SESSION['id_partie'] ?? null;
        $pseudo   = $_SESSION['pseudo']    ?? 'Joueur';

        if (!$idPartie) {
            header('Location: index.php?action=accueil');
            exit;
        }

        $idPage = (int)($_GET['id'] ?? 1);

        // Blocage retour arrière
        $partie = Partie::getById($idPartie);
        if (!$partie || (int)$partie['page_actuelle'] !== $idPage) {
            header('Location: index.php?action=page&id=' . ($partie['page_actuelle'] ?? 1));
            exit;
        }

        $page = Page::getById($idPage);
        if (!$page) {
            header('Location: index.php?action=accueil');
            exit;
        }

        // Remplacement du pseudo dans le texte
        $page['texte'] = str_replace('Takamura', $pseudo, $page['texte']);
        $page['titre'] = str_replace('Takamura', $pseudo, $page['titre']);

        // Si c'est une page de fin → terminer et rediriger
        if ($page['est_fin']) {
            Partie::terminer($idPartie, $page['type_fin'] ?? 'defaite');
            header('Location: index.php?action=fin');
            exit;
        }

        // Image : on lit directement la colonne image de la BDD
        // On vérifie que le fichier existe sur le serveur avec __DIR__
        $imgPage = null;
        if (!empty($page['image'])) {
            $imgAbsolu = __DIR__ . '/../' . $page['image'];
            if (file_exists($imgAbsolu)) {
                $imgPage = $page['image'];
            }
        }

        $choixDisponibles = Choix::getDisponibles($idPage, $idPartie);
        $stats            = Stats::getByPartie($idPartie);
        $inventaire       = Inventaire::getByPartie($idPartie);

        require '../InazumaEleven/Views/page.php';
    }
}