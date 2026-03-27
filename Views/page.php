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

<div class="bg-field"></div>
<div class="bg-lines"></div>

<div class="layout">

    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-logo">⚽ Football Frontier</div>
        <div class="topbar-pseudo">
            Joueur : <span><?= htmlspecialchars($_SESSION['pseudo'] ?? 'Joueur') ?></span>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="sidebar-section">
            <div class="section-titre">Stats</div>
            <?php
                $c = (int)($stats['courage']   ?? 1);
                $t = (int)($stats['technique'] ?? 1);
                $s = (int)($stats['stamina']   ?? 2);
            ?>
            <div class="stat-row">
                <div class="stat-nom">Courage</div>
                <div class="stat-pips">
                    <div class="pip <?= $c >= 1 ? 'courage-on' : 'off' ?>"></div>
                    <div class="pip <?= $c >= 2 ? 'courage-on' : 'off' ?>"></div>
                    <div class="pip <?= $c >= 3 ? 'courage-on' : 'off' ?>"></div>
                </div>
            </div>
            <div class="stat-row">
                <div class="stat-nom">Technique</div>
                <div class="stat-pips">
                    <div class="pip <?= $t >= 1 ? 'technique-on' : 'off' ?>"></div>
                    <div class="pip <?= $t >= 2 ? 'technique-on' : 'off' ?>"></div>
                    <div class="pip <?= $t >= 3 ? 'technique-on' : 'off' ?>"></div>
                </div>
            </div>
            <div class="stat-row">
                <div class="stat-nom">Stamina</div>
                <div class="stat-pips">
                    <div class="pip <?= $s >= 1 ? 'stamina-on' : 'off' ?>"></div>
                    <div class="pip <?= $s >= 2 ? 'stamina-on' : 'off' ?>"></div>
                    <div class="pip <?= $s >= 3 ? 'stamina-on' : 'off' ?>"></div>
                </div>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="section-titre">Inventaire</div>
            <?php if (empty($inventaire)): ?>
                <div class="inventaire-vide">Aucun objet</div>
            <?php else: ?>
                <?php
                    $icones = [
                        'Bandeau de Mark Evans' => '🎀',
                        'Crampons d Axel Blaze' => '👟',
                        'Carnet de Jude Sharp'  => '📒',
                        'Boisson isotonique'    => '🧃',
                    ];
                    foreach ($inventaire as $objet):
                ?>
                <div class="objet-item">
                    <div class="objet-icone"><?= $icones[$objet['nom']] ?? '🎒' ?></div>
                    <div class="objet-info">
                        <div class="objet-nom"><?= htmlspecialchars($objet['nom']) ?></div>
                        <div class="objet-effet">
                            <?php
                                $effets = [];
                                if ($objet['effet_courage']   > 0) $effets[] = '+' . $objet['effet_courage']   . ' Courage';
                                if ($objet['effet_technique'] > 0) $effets[] = '+' . $objet['effet_technique'] . ' Technique';
                                if ($objet['effet_stamina']   > 0) $effets[] = '+' . $objet['effet_stamina']   . ' Stamina';
                                echo implode(' · ', $effets) ?: 'Objet spécial';
                            ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </aside>

    <!-- ZONE DE JEU -->
    <main class="zone-jeu">

        <?php
            // Correspondance id_page → nom de fichier image
            $imgMap = [
                1  => 'Page1.png',
                2  => 'Page2.jpg',
                3  => 'Page3A.jpg',
                4  => 'Page3B.jpg',
                5  => 'Page4A.png',
                6  => 'Page4B.png',
                7  => 'Page4C.png',
                8  => 'Page4D.png',
                9  => 'Page5.jpg',
                10 => 'Page6A.jpg',
                11 => 'Page6A.jpg',  // variante rebond, même image
                12 => 'Page6B.jpg',
                13 => 'Page6C.jpg',
                14 => 'Page7.jpg',
                15 => 'FinVictoire.jpg',
                16 => 'FinSecrete.jpg',
                17 => 'FinDefaite.jpg',
            ];
            $imgFichier = $imgMap[$page['id_page']] ?? null;
            $imgPath    = $imgFichier ? 'public/img/' . $imgFichier : null;
        ?>
        <?php if ($imgPath && file_exists($imgPath)): ?>
            <img class="page-image" src="<?= $imgPath ?>" alt="<?= htmlspecialchars($page['titre']) ?>">
        <?php else: ?>
            <div class="page-image-placeholder">⚽</div>
        <?php endif; ?>

        <div class="carte-texte">
            <div class="carte-header">
                <div class="carte-num">P<?= $page['id_page'] ?></div>
                <div class="carte-titre"><?= htmlspecialchars($page['titre']) ?></div>
            </div>
            <div class="carte-body">
                <div class="texte-histoire">
                    <?php
                        $lignes = explode("\n", nl2br(htmlspecialchars($page['texte'])));
                        foreach ($lignes as $ligne):
                            $brut = strip_tags($ligne);
                            if (str_starts_with(trim($brut), '—') || str_starts_with(trim($brut), '"')):
                    ?>
                        <div class="dialogue"><?= $ligne ?></div>
                    <?php else: ?>
                        <p style="margin-bottom:10px"><?= $ligne ?></p>
                    <?php
                            endif;
                        endforeach;
                    ?>
                </div>
            </div>
        </div>

        <?php if (!empty($choixDisponibles)): ?>
        <div class="carte-choix">
            <div class="choix-header">
                <span style="font-size:1rem">🎯</span>
                <div class="choix-header-txt">Que fais-tu ?</div>
            </div>
            <div class="choix-liste">
                <?php
                    $lettres = ['A', 'B', 'C', 'D'];
                    foreach ($choixDisponibles as $i => $choix):
                ?>
                <form method="POST" action="index.php?action=choisir">
                    <input type="hidden" name="id_choix" value="<?= (int)$choix['id_choix'] ?>">
                    <button class="btn-choix" type="submit">
                        <div class="choix-lettre"><?= $lettres[$i] ?? ($i+1) ?></div>
                        <?= htmlspecialchars($choix['texte_bouton']) ?>
                        <span class="choix-fleche">→</span>
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