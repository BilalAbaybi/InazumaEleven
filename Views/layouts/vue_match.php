<?php
// views/layouts/vue_match.php
// Inclus dans page.php quand type_page = 'match'

$tour         = (int)($_GET['tour'] ?? 1);
$scoreJoueur  = (int)($_GET['sj']   ?? 0);
$scoreAdverse = (int)($_GET['sa']   ?? 0);
$toursMax     = 5;

// Résultat de la dernière action
$resultatAction = $_SESSION['match_resultat_action'] ?? null;
unset($_SESSION['match_resultat_action']);

// Stats adversaires
$statsAdv = json_decode($matchConfig['stats_adversaire'], true) ?? [];
$moyAdv   = !empty($statsAdv) ? round(array_sum($statsAdv) / count($statsAdv), 1) : 2;
?>

<div class="carte" style="overflow:hidden;">

    <!-- Header match -->
    <div style="background:linear-gradient(135deg,rgba(255,107,0,0.15),rgba(26,111,212,0.1));border-bottom:1px solid var(--bordure);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
        <div style="text-align:center;">
            <div style="font-family:'Bangers',cursive;font-size:1.4rem;letter-spacing:1px;color:#fff;">Raimon</div>
            <div style="font-family:'Bangers',cursive;font-size:2.5rem;color:var(--orange);line-height:1;"><?= $scoreJoueur ?></div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:.65rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--texte2);">Tour <?= $tour ?>/<?= $toursMax ?></div>
            <div style="font-family:'Bangers',cursive;font-size:1.2rem;color:var(--texte2);margin-top:4px;">VS</div>
            <?php
            $barresTour = '';
            for ($t = 1; $t <= $toursMax; $t++) {
                $couleur = $t < $tour ? 'var(--orange)' : ($t === $tour ? '#4DA6FF' : 'rgba(255,255,255,0.1)');
                $barresTour .= "<div style='width:20px;height:4px;border-radius:2px;background:{$couleur};'></div>";
            }
            ?>
            <div style="display:flex;gap:3px;justify-content:center;margin-top:6px;"><?= $barresTour ?></div>
        </div>
        <div style="text-align:center;">
            <?php if (!empty($matchConfig['image_adversaire'])): ?>
                <img src="<?= htmlspecialchars($matchConfig['image_adversaire']) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:50%;border:1px solid var(--bordure);">
            <?php endif; ?>
            <div style="font-family:'Bangers',cursive;font-size:1.1rem;letter-spacing:1px;color:#fff;"><?= htmlspecialchars($matchConfig['nom_adversaire']) ?></div>
            <div style="font-family:'Bangers',cursive;font-size:2.5rem;color:var(--rouge);line-height:1;"><?= $scoreAdverse ?></div>
        </div>
    </div>

    <!-- Résultat de la dernière action -->
    <?php if ($resultatAction): ?>
    <div style="padding:12px 20px;background:<?= $resultatAction['succes'] ? 'rgba(46,204,113,0.1)' : 'rgba(231,76,60,0.1)' ?>;border-bottom:1px solid var(--bordure);display:flex;align-items:center;gap:10px;">
        <span style="font-size:1.2rem;"><?= $resultatAction['succes'] ? '✅' : '❌' ?></span>
        <p style="font-size:.88rem;color:var(--texte);font-style:italic;margin:0;"><?= htmlspecialchars($resultatAction['texte']) ?></p>
    </div>
    <?php endif; ?>

    <!-- Description du match -->
    <?php if (!empty($matchConfig['description'])): ?>
    <div style="padding:12px 20px;border-bottom:1px solid var(--bordure);">
        <p style="font-size:.85rem;color:var(--texte2);font-style:italic;"><?= htmlspecialchars($matchConfig['description']) ?></p>
    </div>
    <?php endif; ?>

    <!-- Infos adversaire -->
    <div style="padding:12px 20px;border-bottom:1px solid var(--bordure);display:flex;gap:12px;flex-wrap:wrap;">
        <span style="font-size:.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--texte2);">Force adverse :</span>
        <?php foreach ($statsAdv as $sNom => $sVal): ?>
        <span style="font-size:.7rem;font-weight:700;color:var(--texte2);"><?= ucfirst($sNom) ?> <span style="color:var(--rouge);"><?= $sVal ?>/5</span></span>
        <?php endforeach; ?>
    </div>

    <!-- Actions disponibles -->
    <div style="padding:16px 20px;">
        <div style="font-size:.65rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--texte2);margin-bottom:12px;">⚽ Choisis ton action</div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <?php foreach ($actionsMatch as $i => $action): ?>
            <form method="POST" action="index.php?action=actionMatch">
                <input type="hidden" name="id_match"      value="<?= (int)$matchConfig['id_match'] ?>">
                <input type="hidden" name="id_action"     value="<?= (int)$action['id_action'] ?>">
                <input type="hidden" name="score_joueur"  value="<?= $scoreJoueur ?>">
                <input type="hidden" name="score_adverse" value="<?= $scoreAdverse ?>">
                <input type="hidden" name="tour"          value="<?= $tour ?>">
                <button class="btn-choix" type="submit">
                    <span class="choix-lettre" style="background:<?= match($action['type']) {
                        'tir'       => 'var(--orange)',
                        'dribble'   => '#2ECC71',
                        'passe'     => '#4DA6FF',
                        'technique' => '#9B59B6',
                        'defense'   => '#E74C3C',
                        default     => 'var(--orange)'
                    } ?>;"><?= strtoupper(substr($action['type'], 0, 1)) ?></span>
                    <div>
                        <div style="font-weight:900;"><?= htmlspecialchars($action['nom']) ?></div>
                        <div style="font-size:.7rem;color:var(--texte2);">Utilise : <?= ucfirst($action['stat_utilisee']) ?></div>
                    </div>
                    <span class="choix-fleche">→</span>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
    </div>

</div>