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
        'victoire' => 'Football Frontier Champion !',
        'secrete'  => 'L\'Âme du Football Frontier',
        'defaite'  => 'Ce n\'est que le début',
    ];

    $messages = [
        'victoire' => "Raimon a gagné ! Mark Evans te tend le trophée : \"C'est autant le tien que le nôtre, <strong>$pseudo</strong>.\" Axel hoche la tête. Kevin lève le poing. Jude regarde ailleurs — mais il sourit.",
        'secrete'  => "Dans les dernières secondes, le bandeau de Mark autour de ton front se serre. Tu déclenches le <strong>Cœur Flamboyant</strong>. Mark s'approche après le match : \"Mon grand-père me l'avait montré une fois... Comment tu...\" Il ne finit pas sa phrase.",
        'defaite'  => "Zeus Academy a gagné. Dans les gradins vides, Mark s'assoit à côté de toi sans un mot. Après un long silence : \"Le Football Frontier sera encore là l'année prochaine, <strong>$pseudo</strong>. Et toi aussi.\"",
    ];

    $emojis = [
        'victoire' => '🏆',
        'secrete'  => '✨',
        'defaite'  => '💪',
    ];
?>

<!-- Fond selon le type de fin -->
<div class="bg-<?= htmlspecialchars($typeFin) ?>"></div>
<div class="bg-lines"></div>

<div class="fin-wrapper">

    <!-- Badge type de fin -->
    <div class="badge-fin <?= htmlspecialchars($typeFin) ?>">
        <?= $emojis[$typeFin] ?> <?= strtoupper($titres[$typeFin]) ?>
    </div>

    <!-- Grand titre -->
    <div class="titre-fin <?= htmlspecialchars($typeFin) ?>">
        <?= $titres[$typeFin] ?>
    </div>

    <!-- Image de fin (passée par GameController) -->
    <?php if (!empty($imgFin)): ?>
        <img src="<?= htmlspecialchars($imgFin) ?>"
             alt="Illustration fin"
             style="max-width:500px;width:100%;border-radius:16px;border:1px solid var(--bordure);box-shadow:0 8px 32px rgba(0,0,0,0.5);">
    <?php endif; ?>

    <!-- Message narratif -->
    <div class="carte" style="max-width:600px;width:100%;text-align:center;">
        <p class="message-fin"><?= $messages[$typeFin] ?></p>
    </div>

    <!-- Badge spécial si fin secrète -->
    <?php if ($typeFin === 'secrete'): ?>
        <div class="badge-secret" style="max-width:600px;width:100%;">
            ✨ Tu as découvert la fin secrète — Rejoue pour explorer tous les chemins !
        </div>
    <?php endif; ?>

    <!-- Grille résultats -->
    <div class="grille">

        <!-- Stats finales -->
        <div class="carte">
            <div class="carte-titre-section">Stats finales</div>
            <div class="stats-resultat">

                <div class="stat-ligne">
                    <span style="color:var(--texte2);font-weight:700;font-size:.85rem;">Courage</span>
                    <div class="stat-pips">
                        <div class="pip <?= $stats['courage'] >= 1 ? 'courage-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['courage'] >= 2 ? 'courage-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['courage'] >= 3 ? 'courage-on' : 'off' ?>"></div>
                    </div>
                </div>

                <div class="stat-ligne">
                    <span style="color:var(--texte2);font-weight:700;font-size:.85rem;">Technique</span>
                    <div class="stat-pips">
                        <div class="pip <?= $stats['technique'] >= 1 ? 'technique-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['technique'] >= 2 ? 'technique-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['technique'] >= 3 ? 'technique-on' : 'off' ?>"></div>
                    </div>
                </div>

                <div class="stat-ligne">
                    <span style="color:var(--texte2);font-weight:700;font-size:.85rem;">Stamina</span>
                    <div class="stat-pips">
                        <div class="pip <?= $stats['stamina'] >= 1 ? 'stamina-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['stamina'] >= 2 ? 'stamina-on' : 'off' ?>"></div>
                        <div class="pip <?= $stats['stamina'] >= 3 ? 'stamina-on' : 'off' ?>"></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Résumé chiffré -->
        <div class="carte">
            <div class="carte-titre-section">Résumé</div>
            <div style="display:flex;flex-direction:column;gap:14px;margin-top:4px;">

                <div class="chiffre-bloc">
                    <div class="chiffre-val"><?= (int)$partie['nb_pages_vues'] ?></div>
                    <div class="chiffre-label">Pages visitées</div>
                </div>

                <div class="chiffre-bloc">
                    <div class="chiffre-val"><?= count($inventaire) ?></div>
                    <div class="chiffre-label">Objets collectés</div>
                </div>

                <div class="chiffre-bloc">
                    <div class="chiffre-val"><?= count($historique) ?></div>
                    <div class="chiffre-label">Choix effectués</div>
                </div>

            </div>
        </div>

        <!-- Inventaire final -->
        <?php if (!empty($inventaire)): ?>
        <div class="carte grille-full">
            <div class="carte-titre-section">Objets collectés</div>
            <div class="inventaire-list">
                <?php foreach ($inventaire as $objet): ?>
                    <div class="objet-fin">
                        ⚽ <?= htmlspecialchars($objet['nom']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Boutons -->
    <div class="btns-fin">
        <!-- Rejouer avec le même pseudo -->
        <form method="POST" action="index.php?action=nouvellePartie" style="flex:1;">
            <input type="hidden" name="pseudo" value="<?= $pseudo ?>">
            <button class="btn btn-rejouer" type="submit" style="width:100%;">
                ⚽ Rejouer
            </button>
        </form>

        <!-- Retour accueil pour changer de pseudo -->
        <a href="index.php?action=accueil" class="btn btn-accueil">
            Accueil
        </a>
    </div>

</div>

</body>
</html>