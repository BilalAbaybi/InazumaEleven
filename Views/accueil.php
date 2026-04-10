<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football Frontier</title>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="icon" type="image/png" href="public/img/favicon.png">
    <style>
        #params-panel-acc {
            display:none; position:fixed; bottom:52px; right:16px; z-index:9999;
            background:rgba(8,16,40,0.97); border:1px solid rgba(100,160,255,0.25);
            border-radius:14px; padding:20px 22px; min-width:240px;
            backdrop-filter:blur(12px); box-shadow:0 8px 32px rgba(0,0,0,0.6);
        }
        #params-panel-acc.open { display:block; }
        .param-label-acc { font-size:.68rem; font-weight:900; letter-spacing:2px; text-transform:uppercase; color:#6a9fd4; margin-bottom:8px; display:flex; justify-content:space-between; align-items:center; }
        .param-row-acc { margin-bottom:14px; }
        .param-row-acc:last-child { margin-bottom:0; }
        #btn-params-acc { background:rgba(8,16,40,0.85); border:1px solid rgba(100,160,255,0.2); border-radius:100px; color:#e8edf5; font-size:.75rem; font-weight:700; padding:7px 16px; cursor:pointer; display:flex; align-items:center; gap:6px; }
        #btn-params-acc:hover { background:rgba(20,35,70,0.95); }
    </style>
</head>
<body class="accueil-body">

<div class="accueil-bg-img"></div>
<div class="accueil-overlay"></div>

<div class="accueil-wrapper">

    <img src="public/img/logo.webp" alt="Football Frontier" class="accueil-logo">

    <?php if (!empty($_SESSION['erreur'])): ?>
        <p class="erreur" style="max-width:820px;width:100%;"><?= htmlspecialchars($_SESSION['erreur']) ?></p>
        <?php unset($_SESSION['erreur']); ?>
    <?php endif; ?>

    <div style="display:flex;gap:20px;width:100%;max-width:820px;align-items:flex-start;flex-wrap:wrap;">

        <!-- NOUVELLE PARTIE -->
        <div class="accueil-carte" style="flex:1;min-width:280px;">
            <p style="font-size:.65rem;font-weight:900;letter-spacing:2px;text-transform:uppercase;color:#6a9fd4;margin-bottom:14px;">
                ⚽ Nouvelle partie
            </p>
            <p class="accueil-intro" style="margin-bottom:14px;">
                Tu es <strong>transféré au lycée Raimon</strong> en plein milieu du Football Frontier.
                Crée ton compte pour commencer l'aventure.
            </p>
            <div class="accueil-sep"></div>
            <form method="POST" action="index.php?action=nouvellePartie">
                <label class="form-label">Pseudo</label>
                <input class="ie-input" type="text" name="pseudo"
                       placeholder="Ex : Bilal, Lucas, Enzo..."
                       maxlength="50" required autofocus>
                <label class="form-label">Mot de passe</label>
                <input class="ie-input" type="password" name="mot_de_passe"
                       placeholder="4 caractères minimum" minlength="4" required>
                <button class="ie-btn-active" type="submit">
                    ⚽ COMMENCER L'AVENTURE
                </button>
            </form>
        </div>

        <!-- CONTINUER UNE PARTIE -->
        <div class="accueil-carte" style="flex:1;min-width:280px;">
            <p style="font-size:.65rem;font-weight:900;letter-spacing:2px;text-transform:uppercase;color:#6a9fd4;margin-bottom:14px;">
                💾 Continuer une partie
            </p>
            <p class="accueil-intro" style="margin-bottom:14px;">
                Tu as déjà un compte ? Connecte-toi pour reprendre là où tu t'étais arrêté.
            </p>
            <div class="accueil-sep"></div>
            <form method="POST" action="index.php?action=continuer">
                <label class="form-label">Pseudo</label>
                <input class="ie-input" type="text" name="pseudo"
                       placeholder="Ton pseudo" maxlength="50" required>
                <label class="form-label">Mot de passe</label>
                <input class="ie-input" type="password" name="mot_de_passe"
                       placeholder="Ton mot de passe" required>
                <button class="ie-btn-active" type="submit"
                        style="background:linear-gradient(135deg,#1A6FD4,#4DA6FF);box-shadow:0 6px 20px rgba(26,111,212,0.4);">
                    ▶ REPRENDRE LA PARTIE
                </button>
            </form>
        </div>

    </div>

    <!-- Bouton paramètres -->
    <button id="btn-params-acc" onclick="toggleParamsAcc()">⚙️ Volume</button>

    <!-- Panneau paramètres (volume uniquement) -->
    <div id="params-panel-acc">
        <div class="param-row-acc">
            <span class="param-label-acc">🔊 Volume <span id="vol-val-acc" style="color:#e8edf5;font-weight:700;">70%</span></span>
            <input type="range" id="vol-accueil" min="0" max="100" value="70" step="1"
                   style="width:100%;accent-color:#FF6B00;cursor:pointer;">
        </div>
    </div>

    <p class="accueil-footer">
        Projet BTS SIO — Lycée Fulbert &nbsp;·&nbsp; Histoire dont vous êtes le héros
    </p>

</div>

<!-- Audio opening -->
<audio id="opening-player" preload="auto" loop></audio>

<script>
const player    = document.getElementById('opening-player');
const volSlider = document.getElementById('vol-accueil');
const volValEl  = document.getElementById('vol-val-acc');

// Volume depuis localStorage
const savedVol = localStorage.getItem('ie_volume') ?? '70';
volSlider.value        = savedVol;
volValEl.textContent   = savedVol + '%';
player.volume          = parseInt(savedVol) / 100;

// Lancer l'opening
player.src = 'public/song/opening.mp3';
player.play().catch(() => {
    // Autoplay bloqué → attendre un clic
    document.addEventListener('click', () => {
        player.play().catch(() => {});
    }, { once: true });
});

// Slider volume
volSlider.addEventListener('input', function() {
    player.volume        = this.value / 100;
    volValEl.textContent = this.value + '%';
    localStorage.setItem('ie_volume', this.value);
});

// Toggle panneau
function toggleParamsAcc() {
    document.getElementById('params-panel-acc').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const p = document.getElementById('params-panel-acc');
    const b = document.getElementById('btn-params-acc');
    if (p && !p.contains(e.target) && e.target !== b && !b.contains(e.target)) {
        p.classList.remove('open');
    }
});

// Avant de quitter → sauvegarder le volume, signaler qu'on vient de l'accueil
window.addEventListener('beforeunload', function() {
    localStorage.setItem('ie_volume', volSlider.value);
    localStorage.setItem('ie_from_accueil', '1');
});
</script>
</body>
</html>