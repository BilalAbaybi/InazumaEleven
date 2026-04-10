<?php
// views/layouts/vue_dialogue.php
// Inclus dans page.php quand type_page = 'dialogue'
$tourDialogue = (int)($_GET['d'] ?? 0);
$dialogueCourant = $dialogues[$tourDialogue] ?? null;
$estDernier      = ($tourDialogue >= count($dialogues) - 1);
?>

<div class="dialogue-vn">

    <?php if ($dialogueCourant): ?>

    <!-- Portrait du personnage -->
    <div class="vn-portrait">
        <?php if (!empty($dialogueCourant['perso_image'])): ?>
            <img src="<?= htmlspecialchars($dialogueCourant['perso_image']) ?>"
                 alt="<?= htmlspecialchars($dialogueCourant['perso_nom']) ?>"
                 class="vn-img vn-img--<?= htmlspecialchars($dialogueCourant['expression'] ?? 'normal') ?>">
        <?php endif; ?>
        <div class="vn-perso-nom"><?= htmlspecialchars($dialogueCourant['perso_nom']) ?></div>
        <div class="vn-perso-poste"><?= htmlspecialchars($dialogueCourant['perso_poste']) ?></div>
    </div>

    <!-- Bulle de dialogue -->
    <div class="vn-bulle">
        <div class="vn-bulle-nom"><?= htmlspecialchars($dialogueCourant['perso_nom']) ?></div>
        <p class="vn-texte"><?= nl2br(htmlspecialchars($dialogueCourant['texte'])) ?></p>

        <!-- Indicateurs de progression -->
        <div class="vn-dots">
            <?php foreach ($dialogues as $idx => $d): ?>
                <div class="vn-dot <?= $idx === $tourDialogue ? 'active' : ($idx < $tourDialogue ? 'done' : '') ?>"></div>
            <?php endforeach; ?>
        </div>
    </div>

    </div><!-- .dialogue-vn -->

    <!-- Si ce n'est pas le dernier dialogue → bouton Suivant -->
    <?php if (!$estDernier): ?>
    <div class="carte-choix">
        <div class="choix-liste">
            <a href="index.php?action=page&id=<?= $page['id_page'] ?>&d=<?= $tourDialogue + 1 ?>"
               class="btn-choix" style="text-decoration:none;justify-content:center;">
                Continuer →
            </a>
        </div>
    </div>

    <!-- Dernier dialogue → afficher les réponses -->
    <?php else: ?>
    <?php if (!empty($choixDisponibles)): ?>
    <div class="carte-choix">
        <div class="choix-header"><span class="choix-header-txt">💬 Que réponds-tu ?</span></div>
        <div class="choix-liste">
            <?php foreach ($choixDisponibles as $i => $rep): ?>
            <form method="POST" action="index.php?action=repondre">
                <input type="hidden" name="id_reponse" value="<?= (int)$rep['id_reponse'] ?>">
                <button class="btn-choix" type="submit">
                    <span class="choix-lettre"><?= chr(65 + $i) ?></span>
                    <?= htmlspecialchars($rep['texte_bouton']) ?>
                    <span class="choix-fleche">→</span>
                </button>
            </form>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

<?php else: ?>
    </div>
<?php endif; ?>