<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['titre']) ?> — Football Frontier</title>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<!-- Fond image de la page courante -->
<?php if (!empty($imgPage)): ?>
    <div class="page-bg" style="background-image: url('<?= $imgPage ?>')"></div>
    <div class="page-bg-overlay"></div>
<?php else: ?>
    <div class="bg-field"></div>
    <div class="bg-lines"></div>
<?php endif; ?>

<div class="layout">

    <!-- BARRE DU HAUT -->
    <header class="topbar">
        <div class="topbar-logo">⚽ Football Frontier</div>
        <div class="topbar-pseudo">Joueur : <span><?= htmlspecialchars($_SESSION['pseudo'] ?? '') ?></span></div>
    </header>

    <!-- SIDEBAR STATS -->
    <aside class="sidebar">

        <div class="sidebar-section">
            <div class="section-titre">Stats</div>

            <div class="stat-row">
                <div class="stat-nom">Courage</div>
                <div class="stat-pips">
                    <div class="pip <?= $stats['courage'] >= 1 ? 'courage-on' : 'off' ?>"></div>
                    <div class="pip <?= $stats['courage'] >= 2 ? 'courage-on' : 'off' ?>"></div>
                    <div class="pip <?= $stats['courage'] >= 3 ? 'courage-on' : 'off' ?>"></div>
                </div>
            </div>

            <div class="stat-row">
                <div class="stat-nom">Technique</div>
                <div class="stat-pips">
                    <div class="pip <?= $stats['technique'] >= 1 ? 'technique-on' : 'off' ?>"></div>
                    <div class="pip <?= $stats['technique'] >= 2 ? 'technique-on' : 'off' ?>"></div>
                    <div class="pip <?= $stats['technique'] >= 3 ? 'technique-on' : 'off' ?>"></div>
                </div>
            </div>

            <div class="stat-row">
                <div class="stat-nom">Stamina</div>
                <div class="stat-pips">
                    <div class="pip <?= $stats['stamina'] >= 1 ? 'stamina-on' : 'off' ?>"></div>
                    <div class="pip <?= $stats['stamina'] >= 2 ? 'stamina-on' : 'off' ?>"></div>
                    <div class="pip <?= $stats['stamina'] >= 3 ? 'stamina-on' : 'off' ?>"></div>
                </div>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="section-titre">Inventaire</div>

            <?php if (empty($inventaire)): ?>
                <p class="inventaire-vide">Aucun objet</p>
            <?php else: ?>
                <?php foreach ($inventaire as $objet): ?>
                    <div class="objet-item">
                        <div class="objet-nom"><?= htmlspecialchars($objet['nom']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </aside>

    <!-- CONTENU PRINCIPAL -->
    <main class="zone-jeu">

        <!-- Texte de l'histoire -->
        <div class="carte-texte">
            <div class="carte-header">
                <span class="carte-num">P<?= $page['id_page'] ?></span>
                <span class="carte-titre"><?= htmlspecialchars($page['titre']) ?></span>
            </div>
            <div class="carte-body">
                <p class="texte-histoire"><?= nl2br(htmlspecialchars($page['texte'])) ?></p>
            </div>
        </div>

        <!-- Boutons de choix -->
        <?php if (!empty($choixDisponibles)): ?>
        <div class="carte-choix">
            <div class="choix-header">🎯 Que fais-tu ?</div>
            <div class="choix-liste">
                <?php foreach ($choixDisponibles as $choix): ?>
                <form method="POST" action="index.php?action=choisir">
                    <input type="hidden" name="id_choix" value="<?= (int)$choix['id_choix'] ?>">
                    <button class="btn-choix" type="submit">
                        <?= htmlspecialchars($choix['texte_bouton']) ?> →
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </main>

</div>

</body>
</html>