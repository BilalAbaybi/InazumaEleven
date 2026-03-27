<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football Frontier</title>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="accueil-body">

<!-- Fond image avec overlay -->
<div class="accueil-bg-img"></div>
<div class="accueil-overlay"></div>

<div class="accueil-wrapper">

    <!-- Logo -->
    <img src="public/img/logo.webp" alt="Football Frontier" class="accueil-logo">

    <!-- Carte principale -->
    <div class="accueil-carte">

        <!-- Texte d'intro -->
        <p class="accueil-intro">
            Tu es <strong>transféré au lycée Raimon</strong> en plein milieu du Football Frontier.
            Mark Evans a besoin d'un joueur.<br><br>
            Tes choix détermineront si tu deviendras un champion… ou si tu rentreras chez toi la tête basse.
        </p>

        <!-- Séparateur -->
        <div class="accueil-sep"></div>

        <!-- Stats de départ -->
        <div class="stats-depart-ie">
            <div class="stat-ie">
                <span class="stat-ie-nom">Courage</span>
                <div class="stat-ie-barre">
                    <div class="stat-ie-remplissage courage" style="width:33%"></div>
                </div>
                <span class="stat-ie-val">1/3</span>
            </div>
            <div class="stat-ie">
                <span class="stat-ie-nom">Technique</span>
                <div class="stat-ie-barre">
                    <div class="stat-ie-remplissage technique" style="width:33%"></div>
                </div>
                <span class="stat-ie-val">1/3</span>
            </div>
            <div class="stat-ie">
                <span class="stat-ie-nom">Stamina</span>
                <div class="stat-ie-barre">
                    <div class="stat-ie-remplissage stamina" style="width:66%"></div>
                </div>
                <span class="stat-ie-val">2/3</span>
            </div>
        </div>

        <!-- Séparateur -->
        <div class="accueil-sep"></div>

        <!-- Formulaire -->
        <?php if (!empty($_SESSION['erreur'])): ?>
            <p class="erreur"><?= htmlspecialchars($_SESSION['erreur']) ?></p>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>

        <form method="POST" action="index.php?action=nouvellePartie">
            <label class="form-label">Ton nom de joueur</label>
            <input class="ie-input" type="text" name="pseudo"
                   placeholder="Ex : Lucas, Enzo, Ryu..."
                   maxlength="50" required autofocus>
            <button class="ie-btn-active" type="submit">
                ⚽ COMMENCER L'AVENTURE
            </button>
        </form>

    </div>

    <!-- Footer -->
    <p class="accueil-footer">
        Projet BTS SIO — Lycée Fulbert &nbsp;·&nbsp; Histoire dont vous êtes le héros
    </p>

</div>

</body>
</html>