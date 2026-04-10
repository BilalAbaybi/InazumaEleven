<?php
// views/layouts/sidebar.php
// Barre latérale commune à toutes les pages de jeu
?>
<aside class="sidebar">

    <!-- STATS -->
    <div class="sidebar-section">
        <div class="section-titre">Stats</div>

        <?php
        $statsAffichees = [
            'courage'    => ['label' => 'Courage',    'classe' => 'courage-on',   'max' => 5],
            'technique'  => ['label' => 'Technique',  'classe' => 'technique-on', 'max' => 5],
            'stamina'    => ['label' => 'Stamina',     'classe' => 'stamina-on',   'max' => 5],
            'vitesse'    => ['label' => 'Vitesse',     'classe' => 'vitesse-on',   'max' => 5],
            'chance'     => ['label' => 'Chance',      'classe' => 'chance-on',    'max' => 5],
            'leadership' => ['label' => 'Leadership',  'classe' => 'leadership-on','max' => 5],
        ];
        foreach ($statsAffichees as $key => $cfg):
        ?>
        <div class="stat-row">
            <div class="stat-nom"><?= $cfg['label'] ?></div>
            <div class="stat-pips">
                <?php for ($i = 1; $i <= $cfg['max']; $i++): ?>
                    <div class="pip <?= ($stats[$key] ?? 0) >= $i ? $cfg['classe'] : 'off' ?>"></div>
                <?php endfor; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Barre XP -->
        <?php
        $xpProgress = ($stats['xp_total'] ?? 0) % 100;
        $niveau     = $stats['niveau'] ?? 1;
        ?>
        <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--bordure);">
            <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                <span style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--texte2);">
                    Niv. <?= $niveau ?>
                </span>
                <span style="font-size:.62rem;font-weight:700;color:var(--or);"><?= $xpProgress ?>/100 XP</span>
            </div>
            <div style="height:5px;background:rgba(255,255,255,0.06);border-radius:100px;overflow:hidden;">
                <div style="height:100%;width:<?= $xpProgress ?>%;background:linear-gradient(90deg,#F4D03F,#FF6B00);border-radius:100px;transition:width .5s ease;"></div>
            </div>
        </div>
    </div>

    <!-- AFFINITÉS -->
    <?php if (!empty($affinites)): ?>
    <div class="sidebar-section">
        <div class="section-titre">Relations</div>
        <?php foreach ($affinites as $aff): ?>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
            <?php if (!empty($aff['image'])): ?>
                <img src="<?= htmlspecialchars($aff['image']) ?>" alt="<?= htmlspecialchars($aff['nom']) ?>"
                     style="width:28px;height:28px;border-radius:50%;object-fit:cover;border:1px solid var(--bordure);">
            <?php endif; ?>
            <div style="flex:1;">
                <div style="font-size:.7rem;font-weight:700;color:var(--texte);margin-bottom:2px;">
                    <?= htmlspecialchars($aff['nom']) ?>
                </div>
                <div style="height:4px;background:rgba(255,255,255,0.06);border-radius:100px;overflow:hidden;">
                    <?php
                    $couleurAff = match($aff['type']) {
                        'ami'   => '#2ECC71',
                        'rival' => '#E74C3C',
                        default => '#4DA6FF',
                    };
                    ?>
                    <div style="height:100%;width:<?= (int)$aff['valeur'] ?>%;background:<?= $couleurAff ?>;border-radius:100px;transition:width .5s;"></div>
                </div>
            </div>
            <span style="font-size:.6rem;font-weight:700;color:<?= $couleurAff ?>;"><?= $aff['valeur'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- INVENTAIRE -->
    <div class="sidebar-section">
        <div class="section-titre">Inventaire</div>
        <?php if (empty($inventaire)): ?>
            <p class="inventaire-vide">Aucun objet</p>
        <?php else: ?>
            <?php foreach ($inventaire as $objet): ?>
            <div class="objet-item">
                <span style="font-size:1.1rem;"><?= $objet['icone'] ?? '⚽' ?></span>
                <div>
                    <div class="objet-nom"><?= htmlspecialchars($objet['nom']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</aside>