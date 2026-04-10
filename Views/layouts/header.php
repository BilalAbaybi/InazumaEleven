<?php
// views/layouts/header.php
// Inclus dans toutes les pages de jeu
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['titre'] ?? 'Football Frontier') ?> — Football Frontier</title>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<?php if (!empty($imgPage)): ?>
    <div class="page-bg" style="background-image:url('<?= htmlspecialchars($imgPage) ?>')"></div>
    <div class="page-bg-overlay"></div>
<?php else: ?>
    <div class="bg-field"></div>
    <div class="bg-lines"></div>
<?php endif; ?>

<div class="layout">

<header class="topbar">
    <a href="index.php?action=accueil" style="text-decoration:none;display:flex;align-items:center;">
        <img src="public/img/logo.webp" alt="Football Frontier" style="height:36px;filter:drop-shadow(0 2px 8px rgba(0,150,255,0.5));">
    </a>
    <div style="display:flex;align-items:center;gap:10px;">

        <?php if (!empty($_SESSION['succes'])): ?>
            <span style="font-size:.75rem;font-weight:700;color:#2ECC71;background:rgba(46,204,113,0.12);border:1px solid rgba(46,204,113,0.3);border-radius:8px;padding:4px 10px;">
                ✓ <?= htmlspecialchars($_SESSION['succes']) ?>
            </span>
            <?php unset($_SESSION['succes']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['niveau_up'])): ?>
            <span style="font-size:.75rem;font-weight:700;color:#F4D03F;background:rgba(244,208,63,0.12);border:1px solid rgba(244,208,63,0.4);border-radius:8px;padding:4px 10px;animation:fadeDown .5s ease both;">
                ⭐ Niveau supérieur !
            </span>
            <?php unset($_SESSION['niveau_up']); ?>
        <?php endif; ?>

        <a href="index.php?action=journal"
           style="font-size:.75rem;font-weight:700;color:#9B59B6;background:rgba(155,89,182,0.1);border:1px solid rgba(155,89,182,0.3);border-radius:8px;padding:5px 12px;text-decoration:none;">
            📖 Journal
        </a>
        <a href="index.php?action=sauvegarder"
           style="font-size:.75rem;font-weight:700;color:#4DA6FF;background:rgba(26,111,212,0.12);border:1px solid rgba(77,166,255,0.3);border-radius:8px;padding:5px 12px;text-decoration:none;">
            💾 Sauvegarder
        </a>
        <a href="index.php?action=recommencer"
           onclick="return confirm('Recommencer depuis le début ?')"
           style="font-size:.75rem;font-weight:700;color:#FF6B00;background:rgba(255,107,0,0.1);border:1px solid rgba(255,107,0,0.3);border-radius:8px;padding:5px 12px;text-decoration:none;">
            🔄 Recommencer
        </a>
        <button id="btn-params" onclick="toggleParams()"
                style="background:rgba(255,255,255,0.06);border:1px solid rgba(100,160,255,0.2);border-radius:8px;color:#e8edf5;font-size:.75rem;font-weight:700;padding:5px 12px;cursor:pointer;">
            ⚙️ Paramètres
        </button>
        <div class="topbar-pseudo">Joueur : <span><?= htmlspecialchars($_SESSION['pseudo'] ?? '') ?></span></div>
    </div>
</header>