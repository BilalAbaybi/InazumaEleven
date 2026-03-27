<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Football Frontier — Le Nouveau de Raimon</title>
    <link href="https://fonts.googleapis.com/css2?family=Bangers&family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<div class="bg-field"></div>
<div class="bg-lines"></div>

<div class="accueil-wrapper">

    <div class="badge-tournoi">
        <span class="badge-dot"></span>
        Tournoi en cours
    </div>

    <div class="titre-bloc">
        <div class="titre-sub">Football Frontier</div>
        <div class="titre-main">Le Nouveau<br>de <span>Raimon</span></div>
    </div>

    <div class="carte-accueil">

        <?php if (!empty($_SESSION['erreur'])): ?>
            <div class="erreur"><?= htmlspecialchars($_SESSION['erreur']) ?></div>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>

        <div class="sep">
            <div class="sep-line"></div>
            <div class="sep-icon">⚽</div>
            <div class="sep-line"></div>
        </div>

        <p class="intro-texte">
            Tu es <strong>transféré au lycée Raimon</strong> en plein milieu du
            Football Frontier. Mark Evans a besoin d'un joueur.
            <br><br>
            Tes choix détermineront si tu deviendras un champion… ou si tu rentreras chez toi la tête basse.
        </p>

        <div class="stats-depart">
            <div class="stat-item">
                <div class="stat-label">Courage</div>
                <div class="stat-jauge">
                    <div class="stat-pip on-courage"></div>
                    <div class="stat-pip off"></div>
                    <div class="stat-pip off"></div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Technique</div>
                <div class="stat-jauge">
                    <div class="stat-pip on-technique"></div>
                    <div class="stat-pip off"></div>
                    <div class="stat-pip off"></div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Stamina</div>
                <div class="stat-jauge">
                    <div class="stat-pip on-stamina"></div>
                    <div class="stat-pip on-stamina"></div>
                    <div class="stat-pip off"></div>
                </div>
            </div>
        </div>

        <form method="POST" action="index.php?action=nouvellePartie">
            <label class="form-label" for="pseudo">Ton nom de joueur</label>
            <input
                class="input-pseudo"
                type="text"
                id="pseudo"
                name="pseudo"
                placeholder="Ex : Lucas, Enzo, Ryu..."
                maxlength="50"
                required
                autofocus
            >
            <button class="btn-jouer" type="submit">⚽ Commencer l'aventure</button>
        </form>

    </div>

    <p class="footer-note">
        Projet BTS SIO — Lycée Fulbert &nbsp;·&nbsp; Histoire dont vous êtes le héros
    </p>

</div>

</body>
</html>