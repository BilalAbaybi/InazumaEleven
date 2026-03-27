<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fin — Football Frontier</title>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<?php
    $typeFin = $partie['fin_obtenue'] ?? 'defaite';
    $pseudo  = htmlspecialchars($_SESSION['pseudo'] ?? 'Joueur');

    $titres = [
        'victoire' => '🏆 Football Frontier Champion !',
        'secrete'  => '✨ L\'Âme du Football Frontier',
        'defaite'  => '💪 Ce n\'est que le début',
    ];

    $messages = [
        'victoire' => "Raimon a gagné ! Mark Evans te tend le trophée : \"C'est autant le tien que le nôtre, $pseudo.\"",
        'secrete'  => "Tu as déclenché le Cœur Flamboyant. Mark te regarde : \"Mon grand-père me l'avait montré une fois...\"",
        'defaite'  => "Zeus Academy a gagné. Mark s'assoit à côté de toi : \"Le Football Frontier sera encore là l'année prochaine, $pseudo.\"",
    ];

    // Image : on récupère depuis la page de fin en BDD
    $imgFinPage = Page::getById((int)$partie['page_actuelle']);
    $imgFin = null;
    if (!empty($imgFinPage['image']) && file_exists(__DIR__ . '/../' . $imgFinPage['image'])) {
        $imgFin = $imgFinPage['image'];
    }
?>

<div class="bg-<?= $typeFin ?>"></div>
<div class="bg-lines"></div>

<div class="fin-wrapper">

    <div class="titre-fin <?= $typeFin ?>"><?= $titres[$typeFin] ?></div>

    <!-- Image de fin -->
    <?php if ($imgFin): ?>
        <img src="<?= $imgFin ?>" alt="fin"
             style="max-width:500px;width:100%;border-radius:16px;border:1px solid var(--bordure);">
    <?php endif; ?>

    <!-- Message narratif -->
    <div class="carte" style="max-width:600px;width:100%;text-align:center;">
        <p style="color:var(--texte2);line-height:1.7;"><?= $messages[$typeFin] ?></p>
    </div>

    <!-- Stats finales -->
    <div class="grille">

        <div class="carte">
            <div class="carte-titre-section">Stats finales</div>
            <div class="stats-resultat">
                <div class="stat-ligne">
                    <span style="color:var(--texte2);font-weight:700;">Courage</span>
                    <div class="stat-pips">
                        <div class="pip <?= $stats['courage'] >= 1 ? 'courage-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['courage'] >= 2 ? 'courage-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['courage'] >= 3 ? 'courage-on' : 'off' ?>"></div>
                    </div>
                </div>
                <div class="stat-ligne">
                    <span style="color:var(--texte2);font-weight:700;">Technique</span>
                    <div class="stat-pips">
                        <div class="pip <?= $stats['technique'] >= 1 ? 'technique-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['technique'] >= 2 ? 'technique-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['technique'] >= 3 ? 'technique-on' : 'off' ?>"></div>
                    </div>
                </div>
                <div class="stat-ligne">
                    <span style="color:var(--texte2);font-weight:700;">Stamina</span>
                    <div class="stat-pips">
                        <div class="pip <?= $stats['stamina'] >= 1 ? 'stamina-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['stamina'] >= 2 ? 'stamina-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['stamina'] >= 3 ? 'stamina-on' : 'off' ?>"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="carte">
            <div class="carte-titre-section">Résumé</div>
            <p style="color:var(--texte2);font-size:.9rem;">Pages visitées : <strong style="color:var(--orange)"><?= (int)$partie['nb_pages_vues'] ?></strong></p>
            <p style="color:var(--texte2);font-size:.9rem;margin-top:8px;">Objets collectés : <strong style="color:var(--orange)"><?= count($inventaire) ?></strong></p>
            <p style="color:var(--texte2);font-size:.9rem;margin-top:8px;">Choix effectués : <strong style="color:var(--orange)"><?= count($historique) ?></strong></p>
        </div>

    </div>

    <!-- Boutons -->
    <div class="btns-fin">
        <form method="POST" action="index.php?action=nouvellePartie">
            <input type="hidden" name="pseudo" value="<?= $pseudo ?>">
            <button class="btn btn-rejouer" type="submit">⚽ Rejouer</button>
        </form>
        <a href="index.php?action=accueil" class="btn btn-accueil">Accueil</a>
    </div>

</div>

</body>
</html>