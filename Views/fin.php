<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fin de partie — Football Frontier</title>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<?php
    $typeFin = $partie['fin_obtenue'] ?? 'defaite';
    $pseudo  = htmlspecialchars($_SESSION['pseudo'] ?? 'Joueur');

    $config = [
        'victoire' => [
            'badge'   => '🏆 Victoire',
            'titre'   => 'Football Frontier Champion !',
            'message' => "Raimon a remporté le Football Frontier. Mark Evans t'a tendu le trophée : <strong>\"C'est autant le tien que le nôtre, $pseudo.\"</strong> Axel Blaze hoche la tête. Jude Sharp regarde ailleurs — mais il sourit.",
        ],
        'secrete' => [
            'badge'   => '✨ Fin Secrète',
            'titre'   => "L'Âme du Football Frontier",
            'message' => "Le bandeau de Mark t'a guidé. Tu as déclenché le <strong>Cœur Flamboyant</strong>, une technique que personne ne t'a apprise. Mark te regarde, les yeux écarquillés : <strong>\"Mon grand-père me l'avait montré une fois...\"</strong>",
        ],
        'defaite' => [
            'badge'   => '💪 Défaite',
            'titre'   => "Ce n'est que le début",
            'message' => "Zeus Academy a remporté le trophée. Dans les gradins vides, Mark s'assoit à côté de toi : <strong>\"Le Football Frontier sera encore là l'année prochaine, $pseudo. Et toi aussi.\"</strong>",
        ],
    ];

    $cfg = $config[$typeFin] ?? $config['defaite'];
?>

<div class="bg-<?= $typeFin ?>"></div>
<div class="bg-lines"></div>

<div class="fin-wrapper">

    <div class="badge-fin <?= $typeFin ?>"><?= $cfg['badge'] ?></div>

    <div class="titre-fin <?= $typeFin ?>"><?= $cfg['titre'] ?></div>

    <?php
        $imgFin = [
            'victoire' => 'FinVictoire.jpg',
            'secrete'  => 'FinSecrete.jpg',
            'defaite'  => 'FinDefaite.jpg',
        ];
        $imgFinPath = 'public/img/' . ($imgFin[$typeFin] ?? '');
        if (file_exists($imgFinPath)):
    ?>
        <img src="<?= $imgFinPath ?>" alt="Fin de partie"
             style="max-width:600px;width:100%;border-radius:16px;border:1px solid var(--bordure);box-shadow:0 8px 32px rgba(0,0,0,0.5);">
    <?php endif; ?>

    <div class="grille">

        <div class="carte">
            <div class="carte-titre-section">Stats finales</div>
            <?php
                $c = (int)($stats['courage']   ?? 1);
                $t = (int)($stats['technique'] ?? 1);
                $s = (int)($stats['stamina']   ?? 1);
            ?>
            <div class="stats-resultat">
                <div class="stat-ligne">
                    <div class="stat-label" style="font-size:.85rem;font-weight:700;color:var(--texte2)">Courage</div>
                    <div class="stat-pips">
                        <div class="pip <?= $c >= 1 ? 'courage-on' : 'off' ?>"></div>
                        <div class="pip <?= $c >= 2 ? 'courage-on' : 'off' ?>"></div>
                        <div class="pip <?= $c >= 3 ? 'courage-on' : 'off' ?>"></div>
                    </div>
                </div>
                <div class="stat-ligne">
                    <div class="stat-label" style="font-size:.85rem;font-weight:700;color:var(--texte2)">Technique</div>
                    <div class="stat-pips">
                        <div class="pip <?= $t >= 1 ? 'technique-on' : 'off' ?>"></div>
                        <div class="pip <?= $t >= 2 ? 'technique-on' : 'off' ?>"></div>
                        <div class="pip <?= $t >= 3 ? 'technique-on' : 'off' ?>"></div>
                    </div>
                </div>
                <div class="stat-ligne">
                    <div class="stat-label" style="font-size:.85rem;font-weight:700;color:var(--texte2)">Stamina</div>
                    <div class="stat-pips">
                        <div class="pip <?= $s >= 1 ? 'stamina-on' : 'off' ?>"></div>
                        <div class="pip <?= $s >= 2 ? 'stamina-on' : 'off' ?>"></div>
                        <div class="pip <?= $s >= 3 ? 'stamina-on' : 'off' ?>"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="carte">
            <div class="carte-titre-section">Résumé</div>
            <div style="display:flex;gap:16px;justify-content:space-around;">
                <div class="chiffre-bloc">
                    <div class="chiffre-val"><?= count($historique) ?></div>
                    <div class="chiffre-label">Choix faits</div>
                </div>
                <div class="chiffre-bloc">
                    <div class="chiffre-val"><?= count($inventaire) ?></div>
                    <div class="chiffre-label">Objets</div>
                </div>
                <div class="chiffre-bloc">
                    <div class="chiffre-val"><?= (int)($partie['nb_pages_vues'] ?? 0) ?></div>
                    <div class="chiffre-label">Pages vues</div>
                </div>
            </div>
        </div>

        <div class="carte">
            <div class="carte-titre-section">Inventaire collecté</div>
            <?php if (empty($inventaire)): ?>
                <div class="vide">Aucun objet récupéré</div>
            <?php else: ?>
                <div class="inventaire-list">
                    <?php
                        $icones = [
                            'Bandeau de Mark Evans' => '🎀',
                            'Crampons d Axel Blaze' => '👟',
                            'Carnet de Jude Sharp'  => '📒',
                            'Boisson isotonique'    => '🧃',
                        ];
                        foreach ($inventaire as $obj):
                    ?>
                        <div class="objet-fin">
                            <span><?= $icones[$obj['nom']] ?? '🎒' ?></span>
                            <?= htmlspecialchars($obj['nom']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="carte">
            <div class="carte-titre-section">Histoire</div>
            <p class="message-fin"><?= $cfg['message'] ?></p>
        </div>

        <?php if ($typeFin === 'secrete'): ?>
        <div class="carte grille-full badge-secret">
            ✨ Tu as découvert la <strong>fin secrète</strong> ! Rejoue pour explorer tous les chemins.
        </div>
        <?php endif; ?>

        <div class="grille-full btns-fin" style="padding:0;">
            <a href="#" class="btn btn-rejouer" onclick="event.preventDefault(); document.getElementById('form-rejouer').submit();">
                ⚽ Rejouer
            </a>
            <a href="index.php?action=accueil" class="btn btn-accueil">
                Accueil
            </a>
        </div>

    </div>

</div>

<form id="form-rejouer" method="POST" action="index.php?action=nouvellePartie" style="display:none;">
    <input type="hidden" name="pseudo" value="<?= htmlspecialchars($_SESSION['pseudo'] ?? '') ?>">
</form>

</body>
</html>