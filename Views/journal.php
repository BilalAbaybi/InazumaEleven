<?php
// views/journal.php
$pseudo = $_SESSION['pseudo'] ?? 'Joueur';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal — Football Frontier</title>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="bg-field"></div><div class="bg-lines"></div>

<div style="position:relative;z-index:1;max-width:700px;margin:0 auto;padding:40px 20px;">

    <!-- Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;">
        <div>
            <h1 style="font-family:'Bangers',cursive;font-size:2.5rem;letter-spacing:2px;color:var(--orange);line-height:1;">
                Journal d'aventure
            </h1>
            <p style="font-size:.85rem;color:var(--texte2);margin-top:4px;">
                <?= count($journal) ?> scène<?= count($journal) > 1 ? 's' : '' ?> vécue<?= count($journal) > 1 ? 's' : '' ?>
            </p>
        </div>
        <a href="index.php?action=page&id=<?= $_SESSION['id_partie'] ? (require_once('Models/Partie.php')) || Partie::getById($_SESSION['id_partie'])['page_actuelle'] : 1 ?>"
           style="font-size:.8rem;font-weight:700;color:var(--texte2);background:var(--carte);border:1px solid var(--bordure);border-radius:10px;padding:8px 16px;text-decoration:none;">
            ← Retour au jeu
        </a>
    </div>

    <!-- Entrées du journal -->
    <?php if (empty($journal)): ?>
        <div class="carte" style="text-align:center;padding:40px;">
            <p style="color:var(--texte2);">Aucune scène enregistrée pour l'instant.</p>
        </div>
    <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <?php foreach ($journal as $idx => $entree): ?>
            <div style="display:flex;gap:14px;align-items:flex-start;">
                <!-- Numéro -->
                <div style="width:36px;height:36px;border-radius:50%;background:var(--carte);border:1px solid var(--bordure);display:flex;align-items:center;justify-content:center;font-family:'Bangers',cursive;font-size:1rem;color:var(--orange);flex-shrink:0;">
                    <?= $idx + 1 ?>
                </div>
                <!-- Contenu -->
                <div style="flex:1;background:var(--carte);border:1px solid var(--bordure);border-radius:12px;padding:14px 16px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:.78rem;font-weight:900;color:var(--texte);">
                            <?= htmlspecialchars($entree['titre']) ?>
                        </span>
                        <span style="font-size:.62rem;color:var(--texte2);">
                            <?= date('H:i', strtotime($entree['date_visite'])) ?>
                        </span>
                    </div>
                    <?php if (!empty($entree['image'])): ?>
                        <img src="<?= htmlspecialchars($entree['image']) ?>"
                             style="width:100%;max-height:100px;object-fit:cover;border-radius:8px;margin-bottom:8px;opacity:.7;">
                    <?php endif; ?>
                    <p style="font-size:.82rem;color:var(--texte2);line-height:1.6;margin:0;">
                        <?= htmlspecialchars($entree['resume']) ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</body>
</html>