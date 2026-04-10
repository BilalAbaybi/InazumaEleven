<?php
// views/page.php — fichier unique, tout intégré
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page['titre']) ?> — Football Frontier</title>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="icon" type="image/png" href="public/img/favicon.png">
    <style>
        /* Panneau paramètres */
        #params-panel {
            display:none; position:fixed; top:60px; right:16px; z-index:9999;
            background:rgba(10,15,35,0.97); border:1px solid rgba(100,160,255,0.25);
            border-radius:14px; padding:20px 22px; min-width:260px;
            backdrop-filter:blur(12px); box-shadow:0 8px 32px rgba(0,0,0,0.6);
        }
        #params-panel.open { display:block; }
        .param-label { font-size:.68rem; font-weight:900; letter-spacing:2px; text-transform:uppercase; color:#6a9fd4; margin-bottom:8px; display:flex; justify-content:space-between; align-items:center; }
        .param-row { margin-bottom:18px; }
        .param-row:last-child { margin-bottom:0; }
        .param-slider { width:100%; accent-color:#FF6B00; }
        .param-val { color:#e8edf5; font-weight:700; }
        .param-sep { height:1px; background:rgba(100,160,255,0.15); margin:14px 0; }
        .ost-dot { width:6px; height:6px; border-radius:50%; background:#2ECC71; display:inline-block; animation:pulse-dot 1.5s ease-in-out infinite; }
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.3} }
        /* Notification objet */
        #notif-objet { position:fixed; bottom:44px; left:50%; transform:translateX(-50%) translateY(80px); z-index:9998;
            background:linear-gradient(135deg,rgba(8,16,45,0.97),rgba(15,25,60,0.97));
            border:1px solid rgba(255,180,0,0.6); border-radius:14px; padding:14px 22px;
            display:flex; align-items:center; gap:16px; min-width:320px; max-width:480px;
            box-shadow:0 8px 32px rgba(0,0,0,0.6); opacity:0;
            transition:transform 0.4s cubic-bezier(0.175,0.885,0.32,1.275), opacity 0.4s ease;
            pointer-events:none; }
        #notif-objet.show { transform:translateX(-50%) translateY(0); opacity:1; }
        #notif-event { position:fixed; bottom:90px; left:50%; transform:translateX(-50%) translateY(80px); z-index:9997;
            background:linear-gradient(135deg,rgba(8,16,45,0.97),rgba(15,25,60,0.97));
            border:1px solid rgba(77,166,255,0.5); border-radius:14px; padding:14px 22px;
            display:flex; align-items:center; gap:16px; min-width:320px; max-width:480px;
            opacity:0; transition:transform 0.4s cubic-bezier(0.175,0.885,0.32,1.275), opacity 0.4s ease; pointer-events:none; }
        #notif-event.show { transform:translateX(-50%) translateY(0); opacity:1; }
        #notif-match { position:fixed; bottom:136px; left:50%; transform:translateX(-50%) translateY(80px); z-index:9996;
            background:linear-gradient(135deg,rgba(8,16,45,0.97),rgba(15,25,60,0.97));
            border:1px solid rgba(244,208,63,0.5); border-radius:14px; padding:14px 22px;
            display:flex; align-items:center; gap:16px; min-width:320px; max-width:480px;
            opacity:0; transition:transform 0.4s cubic-bezier(0.175,0.885,0.32,1.275), opacity 0.4s ease; pointer-events:none; }
        #notif-match.show { transform:translateX(-50%) translateY(0); opacity:1; }
        .notif-icone { width:48px; height:48px; border-radius:10px; background:linear-gradient(135deg,rgba(255,150,0,0.25),rgba(255,100,0,0.15)); border:1px solid rgba(255,150,0,0.4); display:flex; align-items:center; justify-content:center; font-size:1.6rem; flex-shrink:0; }
        .notif-texte { flex:1; }
        .notif-label { font-size:.6rem; font-weight:900; letter-spacing:2.5px; text-transform:uppercase; color:rgba(255,180,0,0.8); margin-bottom:3px; }
        .notif-nom { font-size:.95rem; font-weight:900; color:#fff; letter-spacing:.5px; }
        .notif-effet { font-size:.72rem; font-weight:700; color:rgba(160,200,255,0.7); margin-top:2px; }
        .notif-badge { font-family:'Bangers',cursive; font-size:1.1rem; letter-spacing:1px; color:rgba(255,180,0,0.9); flex-shrink:0; }
        /* Barre lecture */
        #now-playing { position:fixed; bottom:0; left:280px; right:0; z-index:20; height:32px;
            background:rgba(6,10,28,0.88); border-top:1px solid rgba(100,160,255,0.12);
            backdrop-filter:blur(8px); display:flex; align-items:center; justify-content:center;
            gap:10px; pointer-events:none; }
        #now-playing-dot { width:6px; height:6px; border-radius:50%; background:#2ECC71; flex-shrink:0; animation:pulse-np 1.5s ease-in-out infinite; }
        @keyframes pulse-np { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.3;transform:scale(.7)} }
        #now-playing-label { font-size:.65rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:rgba(100,140,200,0.5); }
        #now-playing-title { font-size:.72rem; font-weight:900; letter-spacing:1px; color:rgba(180,210,255,0.75); }
        .zone-jeu { padding-bottom:48px; }
        /* Visual novel */
        .dialogue-vn { display:grid; grid-template-columns:160px 1fr; gap:20px; align-items:flex-start; animation:fadeUp .4s ease both; }
        .vn-portrait { display:flex; flex-direction:column; align-items:center; gap:8px; }
        .vn-img { width:160px; height:160px; object-fit:cover; object-position:top 10%; border-radius:50%; border:2px solid var(--orange); box-shadow:0 4px 20px rgba(0,0,0,.5),0 0 0 4px rgba(255,107,0,0.15); transition:transform .3s,filter .3s; }
        .vn-img--content  { filter:brightness(1.05); }
        .vn-img--triste   { filter:brightness(.85) saturate(.7); }
        .vn-img--colere   { filter:brightness(1.1) saturate(1.3); border-color:var(--rouge); }
        .vn-img--surprise { transform:scale(1.03); }
        .vn-perso-nom  { font-family:'Bangers',cursive; font-size:1rem; letter-spacing:1px; color:var(--orange); text-align:center; }
        .vn-perso-poste { font-size:.62rem; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:var(--texte2); text-align:center; }
        .vn-bulle { background:var(--carte); border:1px solid var(--bordure); border-radius:16px; padding:20px; position:relative; box-shadow:0 4px 20px rgba(0,0,0,.3); }
        .vn-bulle::before { content:''; position:absolute; left:-10px; top:30px; width:0; height:0; border-top:8px solid transparent; border-bottom:8px solid transparent; border-right:10px solid var(--bordure); }
        .vn-bulle::after  { content:''; position:absolute; left:-8px;  top:31px; width:0; height:0; border-top:7px solid transparent; border-bottom:7px solid transparent; border-right:9px  solid var(--carte); }
        .vn-bulle-nom { font-family:'Bangers',cursive; font-size:1rem; letter-spacing:1px; color:var(--orange2); margin-bottom:10px; }
        .vn-texte { font-size:1rem; line-height:1.75; color:var(--texte); }
        .vn-dots { display:flex; gap:6px; margin-top:16px; padding-top:12px; border-top:1px solid var(--bordure); }
        .vn-dot { width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15); transition:background .3s; }
        .vn-dot.active { background:var(--orange); border-color:var(--orange); }
        .vn-dot.done   { background:rgba(255,107,0,.3); border-color:rgba(255,107,0,.3); }
    </style>
</head>
<body>

<?php if (!empty($page['image'])): ?>
    <div class="page-bg" style="background-image:url('<?= htmlspecialchars($page['image']) ?>')"></div>
    <div class="page-bg-overlay"></div>
<?php else: ?>
    <div class="bg-field"></div><div class="bg-lines"></div>
<?php endif; ?>

<div class="layout">

<!-- TOPBAR -->
<header class="topbar">
    <a href="index.php?action=accueil" style="text-decoration:none;display:flex;align-items:center;">
        <img src="public/img/logo.webp" alt="Football Frontier" style="height:36px;filter:drop-shadow(0 2px 8px rgba(0,150,255,0.5));">
    </a>
    <div style="display:flex;align-items:center;gap:10px;">
        <?php if (!empty($_SESSION['succes'])): ?>
            <span style="font-size:.75rem;font-weight:700;color:#2ECC71;background:rgba(46,204,113,0.12);border:1px solid rgba(46,204,113,0.3);border-radius:8px;padding:4px 10px;">
                ✓ <?= htmlspecialchars($_SESSION['succes']) ?></span>
            <?php unset($_SESSION['succes']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['niveau_up'])): ?>
            <span style="font-size:.75rem;font-weight:700;color:#F4D03F;background:rgba(244,208,63,0.12);border:1px solid rgba(244,208,63,0.4);border-radius:8px;padding:4px 10px;">⭐ Niveau supérieur !</span>
            <?php unset($_SESSION['niveau_up']); ?>
        <?php endif; ?>
        <a href="index.php?action=journal" style="font-size:.75rem;font-weight:700;color:#9B59B6;background:rgba(155,89,182,0.1);border:1px solid rgba(155,89,182,0.3);border-radius:8px;padding:5px 12px;text-decoration:none;">📖 Journal</a>

        <a href="index.php?action=recommencer" onclick="return confirm('Recommencer ?')" style="font-size:.75rem;font-weight:700;color:#FF6B00;background:rgba(255,107,0,0.1);border:1px solid rgba(255,107,0,0.3);border-radius:8px;padding:5px 12px;text-decoration:none;">🔄 Recommencer</a>
        <button id="btn-inventaire" onclick="toggleInventaire()" style="background:rgba(255,107,0,0.1);border:1px solid rgba(255,107,0,0.3);border-radius:8px;color:#FF9A3C;font-size:.75rem;font-weight:700;padding:5px 12px;cursor:pointer;">🎒 Inventaire <?php if(!empty($inventaire)): ?><span style="background:var(--orange);color:#fff;border-radius:10px;padding:1px 6px;font-size:.65rem;margin-left:3px;"><?= count($inventaire) ?></span><?php endif; ?></button>
        <button id="btn-params" onclick="toggleParams()" style="background:rgba(255,255,255,0.06);border:1px solid rgba(100,160,255,0.2);border-radius:8px;color:#e8edf5;font-size:.75rem;font-weight:700;padding:5px 12px;cursor:pointer;">⚙️ Paramètres</button>
        <div class="topbar-pseudo">Joueur : <span><?= htmlspecialchars($_SESSION['pseudo'] ?? '') ?></span></div>
    </div>
</header>

<!-- PANNEAU PARAMS -->
<div id="params-panel">
    <div class="param-row">
        <span class="param-label">🎵 Musique</span>
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;"><div class="ost-dot"></div><span id="ost-name" style="color:#e8edf5;font-weight:700;font-size:.8rem;">—</span></div>
        <select id="ost-select" style="width:100%;background:rgba(0,10,30,0.7);border:1px solid rgba(100,160,255,0.3);border-radius:8px;padding:8px 10px;color:#e8edf5;font-family:Nunito,sans-serif;font-size:.8rem;font-weight:700;outline:none;cursor:pointer;">
            <option value="auto">🔀 Lecture automatique</option>
            <option value="0">▶ Activate Burning Fase</option>
            <option value="1">▶ Adversity</option>
            <option value="2">▶ Mortal Battle</option>
            <option value="3">▶ Go Raimon !</option>
            <option value="4">▶ One Minute for Miracle</option>
        </select>
    </div>
    <div class="param-sep"></div>
    <div class="param-row"><span class="param-label">🔊 Volume <span class="param-val" id="vol-val">70%</span></span><input type="range" class="param-slider" id="vol-slider" min="0" max="100" value="70" step="1"></div>
</div>

<!-- PANNEAU INVENTAIRE -->
<div id="inventaire-panel" style="display:none;position:fixed;top:60px;right:16px;z-index:9998;background:rgba(10,15,35,0.97);border:1px solid rgba(255,107,0,0.3);border-radius:14px;padding:20px 22px;min-width:280px;max-width:320px;backdrop-filter:blur(12px);box-shadow:0 8px 32px rgba(0,0,0,0.6);">
    <div style="font-size:.68rem;font-weight:900;letter-spacing:2px;text-transform:uppercase;color:#FF9A3C;margin-bottom:14px;display:flex;justify-content:space-between;align-items:center;">
        🎒 Inventaire
        <span style="color:var(--texte2);font-size:.65rem;"><?= count($inventaire) ?> objet(s)</span>
    </div>
    <?php if (empty($inventaire)): ?>
        <p style="font-size:.82rem;color:var(--texte2);font-style:italic;text-align:center;padding:10px 0;">Aucun objet pour l'instant.</p>
    <?php else: ?>
        <?php foreach ($inventaire as $obj): ?>
        <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:rgba(255,255,255,0.03);border:1px solid var(--bordure);border-radius:10px;margin-bottom:8px;">
            <span style="font-size:1.4rem;flex-shrink:0;"><?= $obj['icone']??'⚽' ?></span>
            <div>
                <div style="font-size:.82rem;font-weight:700;color:var(--texte);"><?= htmlspecialchars($obj['nom']) ?></div>
                <div style="font-size:.7rem;color:var(--texte2);margin-top:2px;"><?= htmlspecialchars($obj['description']) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-section">
        <div class="section-titre">Stats</div>
        <?php
        $statsAff = [
            'courage'    => ['Courage',    'courage-on'],
            'technique'  => ['Technique',  'technique-on'],
            'stamina'    => ['Stamina',     'stamina-on'],
            'vitesse'    => ['Vitesse',     'vitesse-on'],
            'chance'     => ['Chance',      'chance-on'],
            'leadership' => ['Leadership',  'leadership-on'],
        ];
        foreach ($statsAff as $k => [$label, $cls]):
        ?>
        <div class="stat-row">
            <div class="stat-nom"><?= $label ?></div>
            <div class="stat-pips">
                <?php for ($i=1;$i<=5;$i++): ?>
                    <div class="pip <?= ($stats[$k]??0)>=$i ? $cls : 'off' ?>"></div>
                <?php endfor; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php $xpP = ($stats['xp_total']??0) % 100; ?>
        <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--bordure);">
            <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                <span style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--texte2);">Niv. <?= $stats['niveau']??1 ?></span>
                <span style="font-size:.62rem;font-weight:700;color:var(--or);"><?= $xpP ?>/100 XP</span>
            </div>
            <div style="height:5px;background:rgba(255,255,255,0.06);border-radius:100px;overflow:hidden;">
                <div style="height:100%;width:<?= $xpP ?>%;background:linear-gradient(90deg,#F4D03F,#FF6B00);border-radius:100px;transition:width .5s;"></div>
            </div>
        </div>
    </div>
    <?php if (!empty($affinites)): ?>
    <div class="sidebar-section">
        <div class="section-titre">Relations</div>
        <?php foreach ($affinites as $aff):
            $c = match($aff['type']){'ami'=>'#2ECC71','rival'=>'#E74C3C',default=>'#4DA6FF'};
        ?>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
            <?php if (!empty($aff['image'])): ?>
                <img src="<?= htmlspecialchars($aff['image']) ?>" style="width:44px;height:44px;border-radius:50%;object-fit:cover;object-position:top center;border:2px solid <?= $c ?>;flex-shrink:0;">
            <?php endif; ?>
            <div style="flex:1;">
                <div style="font-size:.7rem;font-weight:700;color:var(--texte);margin-bottom:2px;"><?= htmlspecialchars($aff['nom']) ?></div>
                <div style="height:4px;background:rgba(255,255,255,0.06);border-radius:100px;overflow:hidden;">
                    <div style="height:100%;width:<?= (int)$aff['valeur'] ?>%;background:<?= $c ?>;border-radius:100px;transition:width .5s;"></div>
                </div>
            </div>
            <span style="font-size:.6rem;font-weight:700;color:<?= $c ?>;"><?= $aff['valeur'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</aside>

<!-- ZONE DE JEU -->
<main class="zone-jeu">

    <div class="carte-texte">
        <div class="carte-header">
            <span class="carte-num">P<?= $page['id_page'] ?></span>
            <span class="carte-titre"><?= htmlspecialchars($page['titre']) ?></span>
            <span style="margin-left:auto;font-size:.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--texte2);">
                <?= match($page['type_page']??'histoire'){'dialogue'=>'💬 Dialogue','match'=>'⚽ Match','evenement'=>'⚡ Événement',default=>'📖 Histoire'} ?>
            </span>
        </div>
        <div class="carte-body">
            <p class="texte-histoire"><?= nl2br(htmlspecialchars($page['texte'])) ?></p>
        </div>
    </div>

    <?php if (($page['type_page']??'histoire') === 'dialogue' && !empty($dialogues)): ?>
        <?php
        $tourDialogue    = (int)($_GET['d'] ?? 0);
        $dialogueCourant = $dialogues[$tourDialogue] ?? null;
        $nbDialogues     = count($dialogues);
        $estDernier      = ($tourDialogue >= $nbDialogues - 1);

        // Récupérer les réponses du dialogue COURANT (pas forcément le dernier)
        $reponsesCourantes = [];
        if ($dialogueCourant) {
            require_once __DIR__ . '/../Models/Dialogue.php';
            $reponsesCourantes = Dialogue::getReponses(
                (int)$dialogueCourant['id_dialogue'],
                $_SESSION['id_partie']
            );
        }
        $aDesReponses = !empty($reponsesCourantes);
        ?>
        <?php if ($dialogueCourant): ?>
        <div class="dialogue-vn">
            <div class="vn-portrait">
                <?php if (!empty($dialogueCourant['perso_image'])): ?>
                    <img src="<?= htmlspecialchars($dialogueCourant['perso_image']) ?>"
                         class="vn-img vn-img--<?= htmlspecialchars($dialogueCourant['expression']??'normal') ?>"
                         alt="<?= htmlspecialchars($dialogueCourant['perso_nom']) ?>">
                <?php endif; ?>
                <div class="vn-perso-nom"><?= htmlspecialchars($dialogueCourant['perso_nom']) ?></div>
                <div class="vn-perso-poste"><?= htmlspecialchars($dialogueCourant['perso_poste']) ?></div>
            </div>
            <div class="vn-bulle">
                <div class="vn-bulle-nom"><?= htmlspecialchars($dialogueCourant['perso_nom']) ?></div>
                <p class="vn-texte"><?= nl2br(htmlspecialchars($dialogueCourant['texte'])) ?></p>
                <div class="vn-dots">
                    <?php foreach ($dialogues as $idx => $d): ?>
                        <div class="vn-dot <?= $idx===$tourDialogue?'active':($idx<$tourDialogue?'done':'') ?>"></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if ($aDesReponses): ?>
        <!-- Ce dialogue a des réponses → les afficher directement -->
        <div class="carte-choix">
            <div class="choix-header"><span class="choix-header-txt">💬 Que réponds-tu ?</span></div>
            <div class="choix-liste">
                <?php foreach ($reponsesCourantes as $i => $rep): ?>
                <form method="POST" action="index.php?action=repondreDialogue">
                    <input type="hidden" name="id_reponse" value="<?= (int)$rep['id_reponse'] ?>">
                    <button class="btn-choix" type="submit">
                        <span class="choix-lettre"><?= chr(65+$i) ?></span>
                        <?= htmlspecialchars($rep['texte_bouton']) ?>
                        <span class="choix-fleche">→</span>
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
        </div>

        <?php elseif (!$estDernier): ?>
        <!-- Pas de réponses sur ce dialogue + pas le dernier → Continuer -->
        <div class="carte-choix"><div class="choix-liste">
            <a href="index.php?action=page&id=<?= $page['id_page'] ?>&d=<?= $tourDialogue+1 ?>"
               class="btn-choix" style="text-decoration:none;justify-content:center;">
                Continuer →
            </a>
        </div></div>

        <?php else: ?>
        <!-- Dernier dialogue sans réponses → avancer via choix normaux si dispo -->
        <?php if (!empty($choixDisponibles)): ?>
        <div class="carte-choix">
            <div class="choix-header"><span class="choix-header-txt">🎯 Que fais-tu ?</span></div>
            <div class="choix-liste">
                <?php foreach ($choixDisponibles as $i => $choix): ?>
                <form method="POST" action="index.php?action=choisir">
                    <input type="hidden" name="id_choix" value="<?= (int)$choix['id_choix'] ?>">
                    <button class="btn-choix" type="submit">
                        <span class="choix-lettre"><?= chr(65+$i) ?></span>
                        <?= htmlspecialchars($choix['texte_bouton']) ?>
                        <span class="choix-fleche">→</span>
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <?php endif; ?>

    <?php elseif (($page['type_page']??'histoire') === 'match' && !empty($matchConfig)): ?>
        <?php
        $tour         = (int)($_GET['tour'] ?? 1);
        $scoreJoueur  = (int)($_GET['sj']   ?? 0);
        $scoreAdverse = (int)($_GET['sa']   ?? 0);
        $toursMax     = 5;
        $ra           = $_SESSION['match_resultat_action'] ?? null;
        unset($_SESSION['match_resultat_action']);
        // Tour 1 : nettoyer les notifs de la page précédente
        if ($tour === 1) {
            unset($_SESSION['match_fin'], $_SESSION['event_aleatoire'], $_SESSION['objet_notif']);
        }
        ?>
        <div class="carte" style="overflow:hidden;">
            <!-- Tableau de score -->
            <div style="background:linear-gradient(135deg,rgba(255,107,0,0.15),rgba(26,111,212,0.1));border-bottom:1px solid var(--bordure);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;">
                <div style="text-align:center;">
                    <div style="font-family:'Bangers',cursive;font-size:1.4rem;color:#fff;">Raimon</div>
                    <div style="font-family:'Bangers',cursive;font-size:2.5rem;color:var(--orange);line-height:1;"><?= $scoreJoueur ?></div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:.65rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--texte2);">Tour <?= $tour ?>/<?= $toursMax ?></div>
                    <div style="font-family:'Bangers',cursive;font-size:1.2rem;color:var(--texte2);">VS</div>
                    <div style="display:flex;gap:3px;justify-content:center;margin-top:6px;">
                        <?php for($t=1;$t<=$toursMax;$t++): ?>
                        <div style="width:18px;height:4px;border-radius:2px;background:<?= $t<$tour?'var(--orange)':($t===$tour?'#4DA6FF':'rgba(255,255,255,0.1)') ?>;"></div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div style="text-align:center;">
                    <div style="font-family:'Bangers',cursive;font-size:1.1rem;color:#fff;"><?= htmlspecialchars($matchConfig['nom_adversaire']) ?></div>
                    <div style="font-family:'Bangers',cursive;font-size:2.5rem;color:var(--rouge);line-height:1;"><?= $scoreAdverse ?></div>
                </div>
            </div>
            <!-- Résultat action précédente avec stats -->
            <?php if ($ra): ?>
            <div style="padding:12px 20px;background:<?= $ra['succes']?'rgba(46,204,113,0.1)':'rgba(231,76,60,0.1)' ?>;border-bottom:1px solid var(--bordure);">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                    <span style="font-size:1.2rem;"><?= $ra['succes']?'✅':'❌' ?></span>
                    <p style="font-size:.88rem;color:var(--texte);font-style:italic;margin:0;"><?= htmlspecialchars($ra['texte']) ?></p>
                </div>
                <?php if (!empty($ra['stat_nom'])): ?>
                <div style="font-size:.7rem;color:var(--texte2);display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span>Stat utilisée : <strong style="color:var(--texte);"><?= ucfirst($ra['stat_nom']) ?></strong></span>
                    <?php
                    $pipsMap = ['courage'=>'courage-on','technique'=>'technique-on','stamina'=>'stamina-on','vitesse'=>'vitesse-on','chance'=>'chance-on','leadership'=>'leadership-on'];
                    $pipCls  = $pipsMap[$ra['stat_nom']] ?? 'courage-on';
                    ?>
                    <div style="display:flex;gap:3px;">
                        <?php for($p=1;$p<=5;$p++): ?>
                        <div class="pip <?= ($ra['stat_valeur']??0)>=$p?$pipCls:'off' ?>" style="width:12px;height:12px;border-radius:3px;"></div>
                        <?php endfor; ?>
                    </div>
                    <span>(<?= $ra['stat_valeur']??'?' ?>/5 · <?= $ra['proba']??0 ?>% de réussite)</span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <!-- Actions disponibles -->
            <div style="padding:16px 20px;">
                <div style="font-size:.65rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--texte2);margin-bottom:12px;">⚽ Choisis ton action</div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <?php foreach ($actionsMatch as $i => $action):
                        $bc = match($action['type']){'tir'=>'var(--orange)','dribble'=>'#2ECC71','passe'=>'#4DA6FF','technique'=>'#9B59B6','defense'=>'#E74C3C',default=>'var(--orange)'};
                        $statAction  = $action['stat_utilisee'];
                        $valAction   = $stats[$statAction] ?? 1;
                        $probaAction = min(95, 30 + ($valAction * 12));
                        $coulProba   = $probaAction >= 70 ? '#2ECC71' : ($probaAction >= 50 ? '#F4D03F' : '#E74C3C');
                    ?>
                    <form method="POST" action="index.php?action=actionMatch">
                        <input type="hidden" name="id_match"      value="<?= (int)$matchConfig['id_match'] ?>">
                        <input type="hidden" name="id_action"     value="<?= (int)$action['id_action'] ?>">
                        <input type="hidden" name="score_joueur"  value="<?= $scoreJoueur ?>">
                        <input type="hidden" name="score_adverse" value="<?= $scoreAdverse ?>">
                        <input type="hidden" name="tour"          value="<?= $tour ?>">
                        <button class="btn-choix" type="submit">
                            <span class="choix-lettre" style="background:<?= $bc ?>;"><?= strtoupper(substr($action['type'],0,1)) ?></span>
                            <div style="flex:1;">
                                <div style="font-weight:900;"><?= htmlspecialchars($action['nom']) ?></div>
                                <div style="font-size:.7rem;color:var(--texte2);margin-top:2px;">
                                    <?= ucfirst($statAction) ?> <?= $valAction ?>/5
                                    · <span style="color:<?= $coulProba ?>;"><?= $probaAction ?>% de réussite</span>
                                </div>
                            </div>
                            <span class="choix-fleche">→</span>
                        </button>
                    </form>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    <?php else: ?>
        <?php if (!empty($choixDisponibles)): ?>
        <div class="carte-choix">
            <div class="choix-header"><span class="choix-header-txt">🎯 Que fais-tu ?</span></div>
            <div class="choix-liste">
                <?php foreach ($choixDisponibles as $i => $choix): ?>
                <form method="POST" action="index.php?action=choisir">
                    <input type="hidden" name="id_choix" value="<?= (int)$choix['id_choix'] ?>">
                    <button class="btn-choix" type="submit">
                        <span class="choix-lettre"><?= chr(65+$i) ?></span>
                        <?= htmlspecialchars($choix['texte_bouton']) ?>
                        <span class="choix-fleche">→</span>
                    </button>
                </form>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="carte-choix">
            <div style="padding:20px;text-align:center;color:var(--texte2);">
                Tes stats actuelles ne te permettent pas de continuer...<br>
                <a href="index.php?action=recommencer" onclick="return confirm('Recommencer ?')"
                   style="color:var(--orange);font-weight:700;text-decoration:none;margin-top:10px;display:inline-block;">
                    Recommencer l'aventure →
                </a>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>

</main>

<!-- NOTIFICATIONS -->
<?php
$notifObjet = null;
if (!empty($_SESSION['objet_notif'])) {
    require_once __DIR__ . '/../Models/Objet.php';
    $notifObjet = Objet::getById((int)$_SESSION['objet_notif']);
    unset($_SESSION['objet_notif']);
}
?>
<div id="notif-objet">
    <div class="notif-icone" id="notif-icone">⚽</div>
    <div class="notif-texte">
        <div class="notif-label">Objet récupéré !</div>
        <div class="notif-nom"  id="notif-nom">—</div>
        <div class="notif-effet" id="notif-effet">—</div>
    </div>
    <div class="notif-badge">NEW</div>
</div>

<?php if (!empty($_SESSION['event_aleatoire'])): ?>
<div id="notif-event" class="show">
    <div class="notif-icone">⚡</div>
    <div class="notif-texte">
        <div class="notif-label">Événement !</div>
        <div class="notif-nom"><?= htmlspecialchars($_SESSION['event_aleatoire']) ?></div>
    </div>
</div>
<?php unset($_SESSION['event_aleatoire']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['match_fin'])): ?>
<?php $mf=$_SESSION['match_fin']; unset($_SESSION['match_fin']); ?>
<div id="notif-match" class="show">
    <div class="notif-icone"><?= $mf['resultat']=='victoire'?'🏆':($mf['resultat']=='nul'?'🤝':'💪') ?></div>
    <div class="notif-texte">
        <div class="notif-label">Fin du match vs <?= htmlspecialchars($mf['adversaire']) ?></div>
        <div class="notif-nom"><?= $mf['resultat']=='victoire'?'Victoire!':($mf['resultat']=='nul'?'Match nul':'Défaite') ?></div>
        <div class="notif-effet"><?= $mf['score_joueur'] ?> - <?= $mf['score_adverse'] ?></div>
    </div>
</div>
<?php endif; ?>

<!-- Barre lecture -->
<div id="now-playing">
    <div id="now-playing-dot"></div>
    <span id="now-playing-label">Lecture en cours&nbsp;:&nbsp;</span>
    <span id="now-playing-title">—</span>
</div>

<audio id="ost-player" preload="auto"></audio>

<script>
<?php if ($notifObjet): ?>
(function(){
    const e=[];
    <?php if((int)$notifObjet['effet_courage']>0):?>e.push('+<?=(int)$notifObjet['effet_courage']?> Courage');<?php endif;?>
    <?php if((int)$notifObjet['effet_technique']>0):?>e.push('+<?=(int)$notifObjet['effet_technique']?> Technique');<?php endif;?>
    <?php if((int)$notifObjet['effet_stamina']>0):?>e.push('+<?=(int)$notifObjet['effet_stamina']?> Stamina');<?php endif;?>
    <?php if((int)$notifObjet['effet_vitesse']>0):?>e.push('+<?=(int)$notifObjet['effet_vitesse']?> Vitesse');<?php endif;?>
    <?php if((int)$notifObjet['effet_chance']>0):?>e.push('+<?=(int)$notifObjet['effet_chance']?> Chance');<?php endif;?>
    <?php if((int)$notifObjet['effet_leadership']>0):?>e.push('+<?=(int)$notifObjet['effet_leadership']?> Leadership');<?php endif;?>
    document.getElementById('notif-nom').textContent=<?=json_encode($notifObjet['nom'])?>;
    document.getElementById('notif-effet').textContent=e.join(' · ')||'Objet spécial';
    document.getElementById('notif-icone').textContent=<?=json_encode($notifObjet['icone']??'⚽')?>;
    setTimeout(()=>document.getElementById('notif-objet').classList.add('show'),300);
    setTimeout(()=>document.getElementById('notif-objet').classList.remove('show'),4300);
})();
<?php endif; ?>
const ne=document.getElementById('notif-event'); if(ne){setTimeout(()=>ne.classList.remove('show'),4000);}
const nm=document.getElementById('notif-match'); if(nm){setTimeout(()=>nm.classList.remove('show'),5000);}

const pistes=['public/song/ost1.mp3','public/song/ost2.mp3','public/song/ost3.mp3','public/song/ost4.mp3','public/song/ost5.mp3'];
const noms=['Activate Burning Fase','Adversity','Mortal Battle','Go Raimon !','One Minute for Miracle'];
const player=document.getElementById('ost-player'),ostSelect=document.getElementById('ost-select'),volSlider=document.getElementById('vol-slider');
const savedVol=localStorage.getItem('ie_volume')??'70';
volSlider.value=savedVol; document.getElementById('vol-val').textContent=savedVol+'%'; player.volume=parseInt(savedVol)/100;
let modeAuto=true,pisteActuelle=0;
const savedMode=localStorage.getItem('ie_mode_ost');
if(savedMode&&savedMode!=='auto'){modeAuto=false;ostSelect.value=savedMode;pisteActuelle=parseInt(savedMode);}
else{const sp=localStorage.getItem('ie_piste');if(sp)pisteActuelle=parseInt(sp);}
const fromAcc=localStorage.getItem('ie_from_accueil');
if(fromAcc==='1'){pisteActuelle=0;localStorage.removeItem('ie_from_accueil');sessionStorage.removeItem('ie_position');sessionStorage.removeItem('ie_piste_session');}
function updateUI(i){document.getElementById('ost-name').textContent=noms[i];document.getElementById('now-playing-title').textContent=noms[i];}
function chargerPiste(i){pisteActuelle=i;player.src=pistes[i];player.volume=parseInt(volSlider.value)/100;updateUI(i);player.play().catch(()=>{});}
player.addEventListener('ended',()=>{modeAuto?chargerPiste((pisteActuelle+1)%pistes.length):(player.currentTime=0,player.play().catch(()=>{}));});
ostSelect.addEventListener('change',function(){if(this.value==='auto'){modeAuto=true;localStorage.setItem('ie_mode_ost','auto');updateUI(pisteActuelle);}else{modeAuto=false;localStorage.setItem('ie_mode_ost',this.value);chargerPiste(parseInt(this.value));}});
const savedPos=parseFloat(sessionStorage.getItem('ie_position')||'0'),savedPisteSS=parseInt(sessionStorage.getItem('ie_piste_session')??pisteActuelle);
if(!fromAcc&&savedPisteSS===pisteActuelle&&savedPos>0){player.src=pistes[pisteActuelle];updateUI(pisteActuelle);player.addEventListener('canplay',function h(){player.removeEventListener('canplay',h);player.currentTime=savedPos;player.play().catch(()=>{document.addEventListener('click',()=>player.play().catch(()=>{}),{once:true});});});}
else{player.src=pistes[pisteActuelle];updateUI(pisteActuelle);player.play().catch(()=>{document.addEventListener('click',()=>player.play().catch(()=>{}),{once:true});});}
volSlider.addEventListener('input',function(){player.volume=this.value/100;document.getElementById('vol-val').textContent=this.value+'%';localStorage.setItem('ie_volume',this.value);});
window.addEventListener('beforeunload',()=>{localStorage.setItem('ie_piste',pisteActuelle);localStorage.setItem('ie_volume',volSlider.value);sessionStorage.setItem('ie_position',player.currentTime);sessionStorage.setItem('ie_piste_session',pisteActuelle);});

function toggleParams(){document.getElementById('params-panel').classList.toggle('open');}
function toggleInventaire(){
    const p=document.getElementById('inventaire-panel');
    p.style.display = p.style.display==='block' ? 'none' : 'block';
}
document.addEventListener('click',function(e){
    const p=document.getElementById('params-panel'),b=document.getElementById('btn-params');
    if(p&&!p.contains(e.target)&&e.target!==b&&!b.contains(e.target))p.classList.remove('open');
    const ip=document.getElementById('inventaire-panel'),ib=document.getElementById('btn-inventaire');
    if(ip&&!ip.contains(e.target)&&e.target!==ib&&!ib.contains(e.target))ip.style.display='none';
});
</script>

</div><!-- .layout -->
</body>
</html>