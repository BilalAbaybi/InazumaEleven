<?php
// views/layouts/footer.php
?>

<!-- Notification objet récupéré -->
<?php
$notifObjet = null;
if (!empty($_SESSION['objet_notif'])) {
    require_once '../InazumaEleven/Models/Objet.php';
    $notifObjet = Objet::getById((int)$_SESSION['objet_notif']);
    unset($_SESSION['objet_notif']);
}
?>
<div id="notif-objet">
    <div class="notif-icone" id="notif-icone">⚽</div>
    <div class="notif-texte">
        <div class="notif-label">Objet récupéré !</div>
        <div class="notif-nom" id="notif-nom">—</div>
        <div class="notif-effet" id="notif-effet">—</div>
    </div>
    <div class="notif-badge">NEW</div>
</div>

<!-- Notification événement aléatoire -->
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

<!-- Notification résultat de match -->
<?php if (!empty($_SESSION['match_fin'])): ?>
<?php $mf = $_SESSION['match_fin']; unset($_SESSION['match_fin']); ?>
<div id="notif-match" class="show">
    <div class="notif-icone"><?= $mf['resultat'] === 'victoire' ? '🏆' : ($mf['resultat'] === 'nul' ? '🤝' : '💪') ?></div>
    <div class="notif-texte">
        <div class="notif-label">Fin du match vs <?= htmlspecialchars($mf['adversaire']) ?></div>
        <div class="notif-nom"><?= $mf['resultat'] === 'victoire' ? 'Victoire !' : ($mf['resultat'] === 'nul' ? 'Match nul' : 'Défaite') ?></div>
        <div class="notif-effet"><?= $mf['score_joueur'] ?> - <?= $mf['score_adverse'] ?></div>
    </div>
</div>
<?php endif; ?>

<!-- Panneau paramètres -->
<div id="params-panel">
    <div class="param-row">
        <span class="param-label">🎵 Musique</span>
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
            <div class="ost-dot"></div>
            <span id="ost-name" style="color:#e8edf5;font-weight:700;font-size:.8rem;">—</span>
        </div>
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
    <div class="param-row">
        <span class="param-label">🔊 Volume <span class="param-val" id="vol-val">70%</span></span>
        <input type="range" class="param-slider" id="vol-slider" min="0" max="100" value="70" step="1">
    </div>
    <div class="param-sep"></div>
    <div class="param-row">
        <span class="param-label">🔍 Échelle <span class="param-val" id="zoom-val">100%</span></span>
        <input type="range" class="param-slider" id="zoom-slider" min="70" max="130" value="100" step="5" style="accent-color:#4DA6FF;">
    </div>
</div>

<!-- Barre lecture en cours -->
<div id="now-playing">
    <div id="now-playing-dot"></div>
    <span id="now-playing-label">Lecture en cours&nbsp;:&nbsp;</span>
    <span id="now-playing-title">—</span>
</div>

<audio id="ost-player" preload="auto"></audio>

<script>
// === NOTIFICATIONS ===
<?php if ($notifObjet): ?>
(function() {
    const effets = [];
    <?php if ((int)$notifObjet['effet_courage']    > 0): ?>effets.push('+<?= (int)$notifObjet['effet_courage'] ?> Courage');<?php endif; ?>
    <?php if ((int)$notifObjet['effet_technique']  > 0): ?>effets.push('+<?= (int)$notifObjet['effet_technique'] ?> Technique');<?php endif; ?>
    <?php if ((int)$notifObjet['effet_stamina']    > 0): ?>effets.push('+<?= (int)$notifObjet['effet_stamina'] ?> Stamina');<?php endif; ?>
    <?php if ((int)$notifObjet['effet_vitesse']    > 0): ?>effets.push('+<?= (int)$notifObjet['effet_vitesse'] ?> Vitesse');<?php endif; ?>
    <?php if ((int)$notifObjet['effet_chance']     > 0): ?>effets.push('+<?= (int)$notifObjet['effet_chance'] ?> Chance');<?php endif; ?>
    <?php if ((int)$notifObjet['effet_leadership'] > 0): ?>effets.push('+<?= (int)$notifObjet['effet_leadership'] ?> Leadership');<?php endif; ?>
    document.getElementById('notif-nom').textContent   = <?= json_encode($notifObjet['nom']) ?>;
    document.getElementById('notif-effet').textContent = effets.join(' · ') || 'Objet spécial';
    document.getElementById('notif-icone').textContent = <?= json_encode($notifObjet['icone'] ?? '⚽') ?>;
    setTimeout(() => document.getElementById('notif-objet').classList.add('show'),    300);
    setTimeout(() => document.getElementById('notif-objet').classList.remove('show'), 4300);
})();
<?php endif; ?>

// Masquer notif event après 4s
const ne = document.getElementById('notif-event');
if (ne) { setTimeout(() => ne.classList.remove('show'), 4000); }
const nm = document.getElementById('notif-match');
if (nm) { setTimeout(() => nm.classList.remove('show'), 5000); }

// === OST ===
const pistes = ['public/song/ost1.mp3','public/song/ost2.mp3','public/song/ost3.mp3','public/song/ost4.mp3','public/song/ost5.mp3'];
const noms   = ['Activate Burning Fase','Adversity','Mortal Battle','Go Raimon !','One Minute for Miracle'];
const player    = document.getElementById('ost-player');
const ostSelect = document.getElementById('ost-select');
const volSlider = document.getElementById('vol-slider');

const savedVol = localStorage.getItem('ie_volume') ?? '70';
volSlider.value = savedVol;
document.getElementById('vol-val').textContent = savedVol + '%';
player.volume = parseInt(savedVol) / 100;

let modeAuto = true;
let pisteActuelle = 0;
const savedMode = localStorage.getItem('ie_mode_ost');
if (savedMode && savedMode !== 'auto') { modeAuto = false; ostSelect.value = savedMode; pisteActuelle = parseInt(savedMode); }
else { const sp = localStorage.getItem('ie_piste'); if (sp) pisteActuelle = parseInt(sp); }

const fromAccueil = localStorage.getItem('ie_from_accueil');
if (fromAccueil === '1') { pisteActuelle = 0; localStorage.removeItem('ie_from_accueil'); sessionStorage.removeItem('ie_position'); sessionStorage.removeItem('ie_piste_session'); }

function updateUI(i) {
    document.getElementById('ost-name').textContent = noms[i];
    document.getElementById('now-playing-title').textContent = noms[i];
}
function chargerPiste(i) {
    pisteActuelle = i; player.src = pistes[i]; player.volume = parseInt(volSlider.value)/100; updateUI(i); player.play().catch(()=>{});
}
player.addEventListener('ended', () => { modeAuto ? chargerPiste((pisteActuelle+1)%pistes.length) : (player.currentTime=0, player.play().catch(()=>{})); });
ostSelect.addEventListener('change', function() {
    if (this.value === 'auto') { modeAuto=true; localStorage.setItem('ie_mode_ost','auto'); updateUI(pisteActuelle); }
    else { modeAuto=false; localStorage.setItem('ie_mode_ost',this.value); chargerPiste(parseInt(this.value)); }
});

const savedPos     = parseFloat(sessionStorage.getItem('ie_position') || '0');
const savedPisteSS = parseInt(sessionStorage.getItem('ie_piste_session') ?? pisteActuelle);
if (!fromAccueil && savedPisteSS === pisteActuelle && savedPos > 0) {
    player.src = pistes[pisteActuelle]; updateUI(pisteActuelle);
    player.addEventListener('canplay', function h() { player.removeEventListener('canplay',h); player.currentTime=savedPos; player.play().catch(()=>{ document.addEventListener('click',()=>player.play().catch(()=>{}),{once:true}); }); });
} else {
    player.src = pistes[pisteActuelle]; updateUI(pisteActuelle);
    player.play().catch(()=>{ document.addEventListener('click',()=>player.play().catch(()=>{}),{once:true}); });
}
volSlider.addEventListener('input', function() { player.volume=this.value/100; document.getElementById('vol-val').textContent=this.value+'%'; localStorage.setItem('ie_volume',this.value); });
window.addEventListener('beforeunload', () => { localStorage.setItem('ie_piste',pisteActuelle); localStorage.setItem('ie_volume',volSlider.value); sessionStorage.setItem('ie_position',player.currentTime); sessionStorage.setItem('ie_piste_session',pisteActuelle); });

// === ZOOM ===
const zoomSlider = document.getElementById('zoom-slider');
const savedZoom  = localStorage.getItem('ie_zoom') ?? '100';
zoomSlider.value = savedZoom; document.body.style.zoom = savedZoom+'%'; document.getElementById('zoom-val').textContent = savedZoom+'%';
zoomSlider.addEventListener('input', function() { document.body.style.zoom=this.value+'%'; document.getElementById('zoom-val').textContent=this.value+'%'; localStorage.setItem('ie_zoom',this.value); });

// === TOGGLE PARAMS ===
function toggleParams() { document.getElementById('params-panel').classList.toggle('open'); }
document.addEventListener('click', function(e) {
    const p=document.getElementById('params-panel'), b=document.getElementById('btn-params');
    if(p && !p.contains(e.target) && e.target!==b && !b.contains(e.target)) p.classList.remove('open');
});
</script>

</div><!-- .layout -->
</body>
</html>