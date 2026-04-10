-- ============================================================
--  FOOTBALL FRONTIER — BDD FINALE CORRIGÉE
--  Reset complet + données propres, IDs dialogues corrects
--  Un seul fichier à exécuter
-- ============================================================

CREATE DATABASE IF NOT EXISTS abaybi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE abaybi;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS historique_match;
DROP TABLE IF EXISTS evenement_aleatoire;
DROP TABLE IF EXISTS scene_secrete;
DROP TABLE IF EXISTS journal;
DROP TABLE IF EXISTS action_match;
DROP TABLE IF EXISTS match_config;
DROP TABLE IF EXISTS reponse_dialogue;
DROP TABLE IF EXISTS dialogue;
DROP TABLE IF EXISTS affinite;
DROP TABLE IF EXISTS personnage;
DROP TABLE IF EXISTS historique_choix;
DROP TABLE IF EXISTS inventaire_joueur;
DROP TABLE IF EXISTS stats_joueur;
DROP TABLE IF EXISTS choix;
DROP TABLE IF EXISTS page;
DROP TABLE IF EXISTS objet;
DROP TABLE IF EXISTS partie;
DROP TABLE IF EXISTS joueur;


-- ============================================================
-- TABLES
-- ============================================================
CREATE TABLE joueur (
    id_joueur INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_creation DATETIME DEFAULT NOW()
);
CREATE TABLE personnage (
    id_personnage INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(80) NOT NULL,
    surnom VARCHAR(80) DEFAULT NULL,
    poste VARCHAR(50) NOT NULL,
    equipe VARCHAR(80) NOT NULL DEFAULT 'Raimon',
    image VARCHAR(200) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    stats_combat TEXT DEFAULT NULL
);
CREATE TABLE page (
    id_page INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    texte TEXT NOT NULL,
    est_fin BOOLEAN DEFAULT FALSE,
    type_fin VARCHAR(20) DEFAULT NULL,
    image VARCHAR(200) DEFAULT NULL,
    resume VARCHAR(300) DEFAULT NULL,
    type_page ENUM('histoire','dialogue','match','evenement') DEFAULT 'histoire'
);
CREATE TABLE objet (
    id_objet INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icone VARCHAR(10) DEFAULT '⚽',
    effet_courage INT DEFAULT 0,
    effet_technique INT DEFAULT 0,
    effet_stamina INT DEFAULT 0,
    effet_vitesse INT DEFAULT 0,
    effet_chance INT DEFAULT 0,
    effet_leadership INT DEFAULT 0
);
CREATE TABLE choix (
    id_choix INT AUTO_INCREMENT PRIMARY KEY,
    id_page_source INT NOT NULL,
    id_page_cible INT NOT NULL,
    texte_bouton VARCHAR(200) NOT NULL,
    cond_courage_min INT DEFAULT 0,
    cond_technique_min INT DEFAULT 0,
    cond_stamina_min INT DEFAULT 0,
    cond_vitesse_min INT DEFAULT 0,
    cond_chance_min INT DEFAULT 0,
    cond_leadership_min INT DEFAULT 0,
    cond_objet_requis INT DEFAULT NULL,
    cond_affinite_perso INT DEFAULT NULL,
    cond_affinite_min INT DEFAULT 0,
    FOREIGN KEY (id_page_source) REFERENCES page(id_page),
    FOREIGN KEY (id_page_cible)  REFERENCES page(id_page),
    FOREIGN KEY (cond_objet_requis) REFERENCES objet(id_objet)
);
CREATE TABLE partie (
    id_partie INT AUTO_INCREMENT PRIMARY KEY,
    id_joueur INT NOT NULL,
    pseudo VARCHAR(50) NOT NULL,
    page_actuelle INT NOT NULL DEFAULT 1,
    date_debut DATETIME DEFAULT NOW(),
    terminee BOOLEAN DEFAULT FALSE,
    fin_obtenue VARCHAR(20) DEFAULT NULL,
    nb_pages_vues INT DEFAULT 1,
    FOREIGN KEY (id_joueur) REFERENCES joueur(id_joueur),
    FOREIGN KEY (page_actuelle) REFERENCES page(id_page)
);
CREATE TABLE stats_joueur (
    id_stats INT AUTO_INCREMENT PRIMARY KEY,
    id_partie INT NOT NULL UNIQUE,
    courage    INT DEFAULT 1 CHECK (courage    BETWEEN 0 AND 5),
    technique  INT DEFAULT 1 CHECK (technique  BETWEEN 0 AND 5),
    stamina    INT DEFAULT 2 CHECK (stamina    BETWEEN 0 AND 5),
    vitesse    INT DEFAULT 1 CHECK (vitesse    BETWEEN 0 AND 5),
    chance     INT DEFAULT 1 CHECK (chance     BETWEEN 0 AND 5),
    leadership INT DEFAULT 1 CHECK (leadership BETWEEN 0 AND 5),
    xp_total INT DEFAULT 0,
    niveau   INT DEFAULT 1,
    FOREIGN KEY (id_partie) REFERENCES partie(id_partie)
);
CREATE TABLE affinite (
    id_affinite INT AUTO_INCREMENT PRIMARY KEY,
    id_partie     INT NOT NULL,
    id_personnage INT NOT NULL,
    valeur INT DEFAULT 50 CHECK (valeur BETWEEN 0 AND 100),
    type ENUM('ami','rival','neutre') DEFAULT 'neutre',
    UNIQUE KEY unique_affinite (id_partie, id_personnage),
    FOREIGN KEY (id_partie)     REFERENCES partie(id_partie),
    FOREIGN KEY (id_personnage) REFERENCES personnage(id_personnage)
);
CREATE TABLE inventaire_joueur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_partie INT NOT NULL,
    id_objet  INT NOT NULL,
    date_obtention DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_partie) REFERENCES partie(id_partie),
    FOREIGN KEY (id_objet)  REFERENCES objet(id_objet)
);
CREATE TABLE historique_choix (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_partie INT NOT NULL,
    id_choix  INT NOT NULL,
    date_choix DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_partie) REFERENCES partie(id_partie),
    FOREIGN KEY (id_choix)  REFERENCES choix(id_choix)
);
CREATE TABLE dialogue (
    id_dialogue   INT AUTO_INCREMENT PRIMARY KEY,
    id_page       INT NOT NULL,
    id_personnage INT NOT NULL,
    texte TEXT NOT NULL,
    ordre INT DEFAULT 1,
    expression VARCHAR(50) DEFAULT 'normal',
    FOREIGN KEY (id_page)       REFERENCES page(id_page),
    FOREIGN KEY (id_personnage) REFERENCES personnage(id_personnage)
);
CREATE TABLE reponse_dialogue (
    id_reponse    INT AUTO_INCREMENT PRIMARY KEY,
    id_dialogue   INT NOT NULL,
    texte_bouton  VARCHAR(200) NOT NULL,
    ordre         INT DEFAULT 1,
    effet_stats   TEXT DEFAULT NULL,
    effet_affinite INT DEFAULT 0,
    id_page_suivante INT DEFAULT NULL,
    condition_json   TEXT DEFAULT NULL,
    FOREIGN KEY (id_dialogue)      REFERENCES dialogue(id_dialogue),
    FOREIGN KEY (id_page_suivante) REFERENCES page(id_page)
);
CREATE TABLE match_config (
    id_match        INT AUTO_INCREMENT PRIMARY KEY,
    id_page         INT NOT NULL,
    nom_adversaire  VARCHAR(80) NOT NULL,
    image_adversaire VARCHAR(200) DEFAULT NULL,
    stats_adversaire TEXT NOT NULL,
    seuil_victoire  INT DEFAULT 3,
    id_page_victoire INT NOT NULL,
    id_page_defaite  INT NOT NULL,
    id_page_nul      INT DEFAULT NULL,
    bonus_victoire   TEXT DEFAULT NULL,
    description      TEXT DEFAULT NULL,
    FOREIGN KEY (id_page)          REFERENCES page(id_page),
    FOREIGN KEY (id_page_victoire) REFERENCES page(id_page),
    FOREIGN KEY (id_page_defaite)  REFERENCES page(id_page)
);
CREATE TABLE action_match (
    id_action     INT AUTO_INCREMENT PRIMARY KEY,
    id_match      INT NOT NULL,
    nom           VARCHAR(80) NOT NULL,
    type          ENUM('tir','dribble','passe','technique','defense') NOT NULL,
    stat_utilisee VARCHAR(30) NOT NULL,
    points_succes INT DEFAULT 1,
    texte_succes  TEXT DEFAULT NULL,
    texte_echec   TEXT DEFAULT NULL,
    condition_json TEXT DEFAULT NULL,
    FOREIGN KEY (id_match) REFERENCES match_config(id_match)
);
CREATE TABLE historique_match (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    id_partie     INT NOT NULL,
    id_match      INT NOT NULL,
    score_joueur  INT DEFAULT 0,
    score_adverse INT DEFAULT 0,
    resultat ENUM('victoire','defaite','nul') NOT NULL,
    date_match DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_partie) REFERENCES partie(id_partie),
    FOREIGN KEY (id_match)  REFERENCES match_config(id_match)
);
CREATE TABLE journal (
    id_entree  INT AUTO_INCREMENT PRIMARY KEY,
    id_partie  INT NOT NULL,
    id_page    INT NOT NULL,
    resume     VARCHAR(300) NOT NULL,
    date_visite DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_partie) REFERENCES partie(id_partie),
    FOREIGN KEY (id_page)   REFERENCES page(id_page)
);
CREATE TABLE scene_secrete (
    id_scene       INT AUTO_INCREMENT PRIMARY KEY,
    id_page        INT NOT NULL,
    id_page_secrete INT NOT NULL,
    condition_json TEXT NOT NULL,
    decouverte     BOOLEAN DEFAULT FALSE,
    description    VARCHAR(200) DEFAULT NULL,
    FOREIGN KEY (id_page)        REFERENCES page(id_page),
    FOREIGN KEY (id_page_secrete) REFERENCES page(id_page)
);
CREATE TABLE evenement_aleatoire (
    id_event      INT AUTO_INCREMENT PRIMARY KEY,
    id_page       INT NOT NULL,
    proba_base    INT DEFAULT 30,
    effet_json    TEXT NOT NULL,
    texte_declenchement      TEXT NOT NULL,
    texte_non_declenchement  TEXT DEFAULT NULL,
    FOREIGN KEY (id_page) REFERENCES page(id_page)
);

-- ============================================================
-- PERSONNAGES (8 — sans doublons)
-- ============================================================
INSERT INTO personnage (nom, surnom, poste, equipe, image, description, stats_combat) VALUES
('Mark Evans',     'Le Mur de Feu',       'Gardien',   'Raimon', 'public/img/perso/mark.png',   'Capitaine de Raimon. Sa détermination est sans égale.',    '{"technique":4,"vitesse":3,"tir":1,"defense":5,"leadership":5}'),
('Axel Blaze',     'La Flamme Solitaire', 'Attaquant',  'Raimon', 'public/img/perso/axel.png',   'Sa Tornade de Feu est imparable.',                         '{"technique":5,"vitesse":4,"tir":5,"defense":2,"leadership":3}'),
('Jude Sharp',     'L\'Œil de Faucon',    'Milieu',    'Raimon', 'public/img/perso/jude.png',   'Stratège de génie. Il analyse tout.',                      '{"technique":5,"vitesse":3,"tir":3,"defense":4,"leadership":4}'),
('Nathan Swift',   'L\'Éclair Vert',      'Défenseur', 'Raimon', 'public/img/perso/nathan.png', 'Le plus rapide de Raimon.',                                 '{"technique":3,"vitesse":5,"tir":2,"defense":4,"leadership":2}'),
('Kevin Dragonfly','Le Dragon Bleu',      'Attaquant', 'Raimon', 'public/img/perso/kevin.png',  'Son Choc du Dragon est dévastateur.',                       '{"technique":4,"vitesse":3,"tir":4,"defense":3,"leadership":3}'),
('Lina',           'La Coach de Raimon',  'Coach',     'Raimon', 'public/img/perso/lina.png',   'Coach de Raimon. Son jugement est toujours juste.',         '{"technique":5,"vitesse":1,"tir":1,"defense":3,"leadership":5}'),
('Celia Hills',    'L\'Ange du Terrain',  'Manager',   'Raimon', 'public/img/perso/celia.png',  'Manager passionnée de Raimon.',                             '{"technique":3,"vitesse":2,"tir":1,"defense":1,"leadership":4}'),
('Nelly Raimon',   'La Fille du Président','Manager',  'Raimon', 'public/img/perso/nelly.png',  'Fille du président du lycée Raimon.',                       '{"technique":2,"vitesse":2,"tir":1,"defense":1,"leadership":5}');

-- ============================================================
-- OBJETS
-- ============================================================
INSERT INTO objet (nom, description, icone, effet_courage, effet_technique, effet_stamina, effet_vitesse, effet_chance, effet_leadership) VALUES
('Bandeau de Mark Evans',  'Le bandeau orange de Mark. "Ne lâche jamais — M.E."',        '🎽', 1, 0, 0, 0, 0, 1),
('Crampons d\'Axel Blaze', 'Ceux avec lesquels Axel a déclenché sa première Tornade.',   '👟', 0, 1, 0, 1, 0, 0),
('Carnet de Jude Sharp',   'Formations secrètes. "Ne pas divulguer."',                   '📓', 0, 1, 0, 0, 0, 1),
('Boisson isotonique',     'Récupère de l\'énergie. +1 Stamina.',                        '🥤', 0, 0, 1, 0, 0, 0),
('Talisman de Raimon',     'Porte-bonheur de l\'équipe. +2 Chance.',                    '🍀', 0, 0, 0, 0, 2, 0),
('Gants de Mark',          'Les gants d\'entraînement de Mark. +1 Technique +1 Courage.','🧤', 1, 1, 0, 0, 0, 0),
('Bracelet de Nathan',     'Le bracelet porte-bonheur de Nathan. +2 Vitesse.',           '💪', 0, 0, 0, 2, 0, 0);

-- ============================================================
-- PAGES
-- ============================================================
INSERT INTO page (id_page, titre, texte, est_fin, type_fin, image, resume, type_page) VALUES
(1,  'Football Frontier — Le Nouveau de Raimon',
'Le Football Frontier. Le tournoi le plus intense du Japon.\n\nTu regardes par la vitre du train qui t\'emmène vers ta nouvelle école. Lycée Raimon. Un nom que tu as entendu mille fois dans les vestiaires de ton ancienne équipe, toujours accompagné des mêmes mots : "Ces gars-là ne sont pas normaux."\n\nTu as été transféré en cours d\'année. Tes affaires tiennent dans un sac. Tout ce que tu sais, c\'est que Raimon participe au Football Frontier cette année — et qu\'ils manquent de joueurs.\n\nTu es Takamura. Et tout commence maintenant.',
FALSE, NULL, 'public/img/Page1.png', 'Arrivée en train au lycée Raimon.', 'histoire'),

(2,  'Le lycée Raimon',
'Le lycée Raimon est plus grand que tu ne l\'imaginais. Des bâtiments lumineux, une cour immense, et au fond — un terrain de football impeccable.\n\nTu trouves ton chemin jusqu\'à la salle d\'accueil quand tu l\'entends.\n\nUn bruit sourd. Puis un autre. Comme si quelqu\'un frappait dans un mur.\n\nTu suis le son jusqu\'aux gradins du terrain. Ce que tu vois te coupe le souffle.\n\nMark Evans — tu le reconnais immédiatement — est seul dans les buts. Axel Blaze lui tire dessus, encore et encore, de plus en plus fort. Et chaque fois, Mark arrête tout. Sa Main Céleste explose à chaque frappe comme une barrière vivante.\n\nIls s\'arrêtent quand ils te remarquent.',
FALSE, NULL, 'public/img/Page2.jpg', 'Première vision de Mark et Axel à l\'entraînement.', 'histoire'),

(3,  'Rencontre avec Mark Evans',
'Tu t\'approches de Mark Evans et Axel Blaze. Mark remarque ta présence et vient vers toi.',
FALSE, NULL, 'public/img/Page2.jpg', 'Mark Evans te propose de rejoindre l\'équipe.', 'dialogue'),

(4,  'Premier jour dans l\'équipe',
'Mark t\'accueille avec un grand sourire et te présente à toute l\'équipe réunie sur le terrain.\n\n— "Les gars, voilà Takamura. Notre nouveau coéquipier."\n\nNathan Swift te serre la main en premier, un grand sourire aux lèvres.\n— "Bienvenue dans la meilleure équipe du Japon !"\n\nKevin Dragonfly lève le menton dans ta direction, silencieux mais pas hostile.\n\nJude Sharp t\'étudie une seconde, puis dit simplement :\n— "Montre-nous ce que tu vaux. Les mots ne servent à rien ici."\n\nAxel ne dit rien. Il repart déjà botter dans le filet.',
FALSE, NULL, 'public/img/Page3A.jpg', 'Intégration dans l\'équipe de Raimon.', 'histoire'),

(5,  'Rencontre avec Axel Blaze',
'Après le briefing, tu croises Axel Blaze seul dans le couloir des vestiaires.',
FALSE, NULL, 'public/img/Page3A.jpg', 'Rencontre avec Axel Blaze dans le couloir.', 'dialogue'),

(6,  'Rencontre avec Jude Sharp',
'Tu trouves Jude Sharp dans la bibliothèque, son carnet de tactiques ouvert devant lui.',
FALSE, NULL, 'public/img/Page3A.jpg', 'Jude Sharp te donne une leçon de tactique.', 'dialogue'),

(7,  'Rencontre avec Nathan Swift',
'Nathan Swift t\'intercepte à la sortie des vestiaires, débordant d\'énergie comme toujours.',
FALSE, NULL, 'public/img/Page3A.jpg', 'Nathan Swift t\'accueille avec enthousiasme.', 'dialogue'),

(8,  'Rencontre avec Kevin Dragonfly',
'Kevin Dragonfly est seul au bord du terrain. Tu t\'approches sans bruit.',
FALSE, NULL, 'public/img/Page3A.jpg', 'Kevin Dragonfly te parle de l\'esprit de Raimon.', 'dialogue'),

(9,  'Le match d\'entraînement',
'Le lendemain matin, Lina réunit toute l\'équipe sur le terrain.\n\n— "Avant de parler du Football Frontier, je veux voir ce dont vous êtes capables ensemble. Takamura, tu joues avec le groupe B contre le groupe A. Considérez ça comme un vrai match. Pas de cadeau."\n\nMark te regarde depuis l\'autre côté du terrain et te fait un signe de tête. Un défi.\n\nLe sifflet retentit. C\'est ton premier vrai test à Raimon.',
FALSE, NULL, 'public/img/Page3A.jpg', 'Match d\'entraînement interne à Raimon.', 'match'),

(10, 'Après l\'entraînement — Le vestiaire',
'La séance est terminée. Quoi qu\'il se soit passé sur le terrain, Lina réunit l\'équipe dans le vestiaire.\n\n— "Pas mal. Mais on a encore du travail. Le Football Frontier commence dans trois semaines. Et je vous garantis que la Royal Academy est déjà en train de préparer quelque chose contre nous."\n\nMark se lève.\n— "On les a battus avant. On peut les battre encore. La question c\'est : est-ce qu\'on veut vraiment gagner ?"\n\nToute l\'équipe répond d\'un regard. Et toi — pour la première fois depuis longtemps — tu te sens à ta place.',
FALSE, NULL, 'public/img/Page3A.jpg', 'Briefing après le match d\'entraînement.', 'histoire'),

(11, 'Les jours suivants — Choix de chemin',
'Les jours qui suivent sont intenses. Lina organise des entraînements deux fois par jour. Tu dois décider comment utiliser ton temps libre pour progresser le plus vite possible.',
FALSE, NULL, 'public/img/Page3B.jpg', 'Tu choisis comment te préparer.', 'histoire'),

(12, 'S\'entraîner avec Mark',
'Tu demandes à Mark de t\'entraîner individuellement. Il accepte sans hésiter.\n\nPendant deux heures, vous travaillez les arrêts, les placements, la résistance mentale. Mark ne te ménage pas.\n\n— "Encore. Le gardien de la Royal Academy arrêtera chaque tir raté. Tu dois tirer comme si ta vie en dépendait."\n\nÀ la fin, épuisé, tu t\'effondres sur le gazon. Mark s\'assoit à côté de toi.\n— "T\'as du cœur, Takamura. C\'est ça qui fait un vrai joueur."\n\nIl te tend ses vieux gants d\'entraînement.\n— "Garde-les. Ils m\'ont porté chance pendant deux ans."',
FALSE, NULL, 'public/img/Page4A.png', 'Entraînement intensif avec Mark. Tu reçois ses gants.', 'histoire'),

(13, 'Étudier avec Jude',
'Jude accepte de te transmettre ses analyses tactiques pendant une soirée entière dans la bibliothèque.\n\nVous passez des heures sur les formations adverses, les failles de la Royal Academy, les schémas d\'attaque de Zeus Academy.\n\n— "Retiens ça : leur gardien anticipe toujours à gauche. Et leur défenseur central a un angle mort à droite. Si tu arrives à exploiter ça..."\n\nQuand tu rentres, ta tête est pleine d\'informations. Tu ranges le carnet de notes que Jude t\'a finalement glissé dans les mains avant de partir.\n— "Tu en auras besoin plus que moi."',
FALSE, NULL, 'public/img/Page4C.jpg', 'Soirée tactique avec Jude. Tu reçois ses notes.', 'histoire'),

(14, 'Courir avec Nathan',
'Nathan t\'emmène faire des sprints à l\'aube, avant que tout le monde soit réveillé.\n\nVous courez, courez encore, jusqu\'à ce que tes jambes ne répondent plus. Nathan, lui, semble ne jamais se fatiguer.\n\n— "La vitesse, c\'est pas juste les jambes. C\'est la tête. Tu dois décider avant de partir. Zéro hésitation."\n\nAu bout d\'une semaine, tu es plus rapide. Nettement. Nathan te tend son bracelet porte-bonheur avec un grand sourire.\n— "Pour toi. Il m\'a accompagné depuis mes débuts. Maintenant c\'est ton tour."',
FALSE, NULL, 'public/img/Page4B.png', 'Entraînement de vitesse avec Nathan. Tu reçois son bracelet.', 'histoire'),

(15, 'Se préparer seul',
'Tu décides de t\'entraîner seul. Les nuits sur le terrain vide, les sprints à l\'aube, les tirs contre le mur.\n\nC\'est solitaire. Mais tu apprends à te connaître, à comprendre tes limites et à les repousser toi-même.\n\nUn soir, Kevin te retrouve sur le terrain. Il s\'assoit dans les gradins et te regarde sans rien dire pendant une heure. Puis, quand tu t\'arrêtes :\n— "Continue comme ça."\n\nC\'est tout. Mais venant de Kevin, c\'est presque un discours.',
FALSE, NULL, 'public/img/Page4D.png', 'Entraînement solitaire. Tu t\'endurcis.', 'histoire'),

(16, 'Match amical — La leçon humiliante',
'Une semaine avant les qualifications, Lina annonce un match amical contre la Royal Academy.\n\nDans les vestiaires, l\'ambiance est lourde. Tout le monde sait ce que ça veut dire.\n\n— "La Royal Academy est la meilleure équipe du Japon en ce moment," dit Lina. "Ce match va nous montrer exactement où on en est. Prenez des notes. Mémorisez chaque erreur."\n\nMark serre les poings.\n— "On peut les battre."\n\nAxel ne dit rien. Il regarde le sol.',
FALSE, NULL, 'public/img/Page5.jpg', 'Avant le match amical contre la Royal Academy.', 'histoire'),

(17, 'Défaite contre la Royal Academy',
'C\'est une leçon brutale.\n\nLa Royal Academy marque dès la troisième minute. Puis encore. Leurs techniques sont d\'un autre niveau. Le Triangle de la Mort de leur capitaine transperce votre défense trois fois en première mi-temps.\n\nMark arrête l\'impossible, encore et encore. Mais il ne peut pas tout faire seul.\n\nLe coup de sifflet final retentit. 0-3.\n\nMark t\'aide à te relever depuis le gazon.\n— "Ça fait mal. C\'est normal. Mais si t\'oublies cette douleur, tu n\'apprendras jamais."',
FALSE, NULL, 'public/img/Page6C.jpg', 'Défaite 0-3 contre la Royal Academy.', 'histoire'),

(18, 'Après la défaite — Dialogue clé',
'Dans le vestiaire après le match, le silence est pesant. Lina laisse l\'équipe seule quelques minutes.',
FALSE, NULL, 'public/img/Page5.jpg', 'Après la défaite. L\'équipe se resserre.', 'dialogue'),

(19, 'Football Frontier — Qualifications : Premier tour',
'Le Football Frontier est officiellement lancé.\n\nVous arrivez au terrain du Collège Wild sous un ciel gris. Les gradins sont à moitié pleins.\n\nLina vous réunit avant le coup d\'envoi.\n— "Wild n\'est pas une équipe à sous-estimer. Ils sont physiques et jouent sur la fatigue adverse. Commencez prudemment. Et Takamura — c\'est ton premier match officiel. Fais-toi confiance."\n\nMark te regarde.\n— "On est ensemble. Quoi qu\'il arrive."',
FALSE, NULL, 'public/img/Page5.jpg', 'Qualifications — Premier tour vs Collège Wild.', 'histoire'),

(20, 'Match contre Wild',
'Wild joue exactement comme Lina l\'avait prédit — agressif, physique, bruyant.\n\nDès les premières minutes, leurs attaquants foncent sur vous comme des boulets de canon. Mark tient bon. Nathan neutralise leur ailier droit avec aisance.\n\nLa mi-temps arrive sur un score de 0-0.\n\nDans les vestiaires, Axel se lève.\n— "Je marque dans les dix premières minutes de la seconde mi-temps. Mettez-moi le ballon."',
FALSE, NULL, 'public/img/Page5.jpg', 'Match contre Wild. 0-0 à la mi-temps.', 'match'),

(21, 'Victoire contre Wild — 1-0',
'Le Trampoline du Tonnerre d\'Axel perce le gardien de Wild comme un boulet de feu.\n\nLe stade explose. Wild essaie de réagir mais Mark est infranchissable. Le match se termine 1-0.\n\nDans les vestiaires, Nathan danse entre les bancs.\n— "On est lancés ! Personne nous arrête !"\n\nAxel range ses affaires sans un mot. Mais il y a quelque chose dans ses yeux. Une flamme.',
FALSE, NULL, 'public/img/Page6B.jpg', 'Victoire 1-0 contre Wild. Axel marque.', 'histoire'),

(22, 'Deuxième tour — Cybertech',
'Lina et Jude analysent les vidéos de Cybertech avant le match.',
FALSE, NULL, 'public/img/Page5.jpg', 'Avant le match contre Cybertech.', 'dialogue'),

(23, 'Match contre Cybertech',
'Cybertech prend l\'avantage rapidement. 1-0 pour eux à la mi-temps.\n\nLina est calme dans les vestiaires.\n— "Exactement ce que j\'attendais. Ils ont montré leur système. Maintenant on va l\'exploiter."\n\nJude se lève et explique en trente secondes comment contourner leur pressing.\n\nLa seconde mi-temps est différente. Kevin et Axel exécutent la Tornade du Dragon. 1-1. Puis Axel déclenche la Foudre. 2-1.',
FALSE, NULL, 'public/img/Page5.jpg', 'Match contre Cybertech.', 'match'),

(24, 'Victoire contre Cybertech — 2-1',
'Le coup de sifflet final retentit sur une victoire 2-1.\n\nCybertech quitte le terrain sans un mot. Leurs algorithmes n\'avaient pas prévu Raimon.\n\nMark t\'attrape par l\'épaule.\n— "Tu vois ce qui se passe quand on joue ensemble ? C\'est ça, Raimon."\n\nLina acquiesce depuis le coin.\n— "On passe en demi-finale. Préparez-vous. Otaku nous attend."',
FALSE, NULL, 'public/img/Page6B.jpg', 'Victoire 2-1 contre Cybertech.', 'histoire'),

(25, 'Demi-finale Kantō — Raimon vs Otaku',
'La demi-finale vous oppose au Collège Otaku.\n\nOtaku n\'est pas comme les équipes précédentes. Ils jouent collectif, intelligemment, avec une technicité impressionnante.\n\nLe soir avant le match, Mark réunit l\'équipe.\n— "Otaku est forte. Vraiment forte. Mais on a quelque chose qu\'ils n\'ont pas."\n\nIl marque une pause.\n— "On joue pour quelque chose de plus grand que la victoire. Et ça, ça fait toute la différence."',
FALSE, NULL, 'public/img/Page5.jpg', 'Demi-finale Kantō contre Otaku.', 'histoire'),

(26, 'Match contre Otaku',
'Otaku est coriace. Leur gardien arrête tout, leur défense est organisée.\n\nIls marquent en premier. 0-1.\n\nL\'équipe ne lâche pas. William Glass équalize d\'un Coup de Willy spectaculaire. 1-1.\n\nEnsuite c\'est Kevin — Kevin Dragonfly qui déclenche son Choc du Dragon dans un silence de cathédrale. 2-1.\n\nRaimon tient jusqu\'au coup de sifflet final.',
FALSE, NULL, 'public/img/Page5.jpg', 'Match contre Otaku.', 'match'),

(27, 'Victoire contre Otaku — Finale Kantō',
'Raimon 2 - Otaku 1.\n\nLes joueurs s\'effondrent sur le terrain, épuisés mais souriants. Nathan fait des tours de terrain en criant.\n\nMark lève les yeux vers le ciel quelques secondes. Puis il te regarde.\n— "On est en finale. Contre la Royal Academy. Tu sais ce que ça veut dire ?"\n\nTu hoches la tête.\n— "C\'est notre revanche. Et cette fois, on sera prêts."',
FALSE, NULL, 'public/img/Page6B.jpg', 'Victoire 2-1 contre Otaku. Finale Kantō !', 'histoire'),

(28, 'La finale Kantō — Royal Academy',
'Dans les vestiaires de la Royal Academy, la tension est palpable avant la finale Kantō.',
FALSE, NULL, 'public/img/Page5.jpg', 'Finale Kantō. Royal Academy nous attend.', 'dialogue'),

(29, 'Match — Finale Kantō',
'La Royal Academy ouvre le score rapidement. Mark fait un arrêt extraordinaire sur le deuxième tir mais ne peut rien sur le troisième. 0-1 à la mi-temps.\n\nDans les vestiaires, le silence est total. Puis Axel se lève, lentement.\n— "J\'en ai marre de perdre contre eux."',
FALSE, NULL, 'public/img/Page5.jpg', 'Finale Kantō contre Royal Academy.', 'match'),

(30, 'Victoire en finale Kantō — 2-1',
'La seconde mi-temps est une démonstration.\n\nKevin et Axel déclenchent la Tornade du Dragon. 1-1.\n\nEnsuite, sur une action collective magnifique, Axel et Mark combinent pour le Super Trampoline du Tonnerre. 2-1.\n\nLa Royal Academy se bat jusqu\'au bout mais Mark est infranchissable.\n\nCoupe de sifflet. 2-1 pour Raimon.\n\nNathan pleure. Kevin sourit. Jude range son carnet — mais ses yeux brillent.\n\nMark lève les bras vers le ciel.\n\nRaimon est champion Kantō.',
FALSE, NULL, 'public/img/Page6B.jpg', 'Victoire 2-1 en finale Kantō. Champions régionaux !', 'histoire'),

(31, 'Vers le tournoi national',
'La qualification au tournoi national change tout.\n\nLes journaux parlent de Raimon. Et surtout — Zeus Academy entre dans les conversations.\n\nZeus Academy. L\'équipe qui a battu la Royal Academy 10-0.\n\nLina convoque une réunion extraordinaire.\n— "Zeus Academy ne joue pas au football. Ils dominent. Chaque joueur de Zeus a des capacités qui dépassent ce qu\'on a jamais vu. Si on veut avoir une chance, chacun d\'entre vous doit progresser à un niveau jamais atteint."',
FALSE, NULL, 'public/img/Page5.jpg', 'Qualification au national. Zeus Academy apparaît.', 'histoire'),

(32, 'Tournoi National — Premier tour vs Shuriken',
'Le stade du Football Frontier est une autre dimension.\n\nDes milliers de spectateurs. Des caméras partout.\n\nVotre adversaire : Shuriken. Une équipe du Kansai connue pour son jeu rapide.\n\nLina est concentrée.\n— "Shuriken joue sur la vitesse et la surprise. Nathan — tu es l\'homme clé. Takamura — fais confiance à ton instinct."',
FALSE, NULL, 'public/img/Page5.jpg', 'Tournoi national — 1er tour vs Shuriken.', 'histoire'),

(33, 'Match contre Shuriken',
'Shuriken marque en premier. 0-1.\n\nL\'équipe ne panique pas.\n\nNathan Swift répond d\'un Oiseau de Feu — une technique travaillée en secret. Le stade explose. 1-1.\n\nEnsuite Axel déclenche sa Tornade de Feu dans les dernières minutes. 2-1.',
FALSE, NULL, 'public/img/Page5.jpg', 'Match contre Shuriken.', 'match'),

(34, 'Victoire contre Shuriken — 2-1',
'2-1 pour Raimon. Premier tour du national validé.\n\nDans le couloir après le match, vous croisez les joueurs de Zeus Academy qui venaient d\'écraser leur adversaire 8-0.\n\nLeur capitaine s\'arrête et vous regarde passer. Il n\'a pas l\'air impressionné.\n\nMark soutient son regard sans ciller.\n— "On les verra en finale," murmure-t-il.',
FALSE, NULL, 'public/img/Page6B.jpg', 'Victoire 2-1 contre Shuriken. Croisement avec Zeus.', 'histoire'),

(35, 'Deuxième tour — Terria',
'Terria est une révélation du tournoi national. Personne ne les attendait en deuxième tour.\n\nLeur force : l\'unité absolue.\n\nJude analyse leurs vidéos toute la nuit.\n— "Leur point faible est leur gardien. Il sort trop souvent. Une frappe rasante sur l\'angle droit les battra."\n\nKevin hoche la tête.\n— "Je gère ça."',
FALSE, NULL, 'public/img/Page5.jpg', 'Deuxième tour national vs Terria.', 'histoire'),

(36, 'Match contre Terria',
'Terria joue avec une intensité impressionnante. Ils prennent l\'avantage sur un Tir Aveuglant. 0-1.\n\nMais Raimon répond. Jude déclenche l\'Éclair Pulvérisant. 1-1.\n\nKevin conclut sur un Choc du Dragon que le gardien de Terria, sorti trop tôt, ne peut que regarder passer. 2-1.\n\nRaimon est en demi-finale nationale.',
FALSE, NULL, 'public/img/Page5.jpg', 'Match contre Terria.', 'match'),

(37, 'Victoire contre Terria — Demi-finale',
'2-1. Raimon en demi-finale nationale.\n\nCe soir-là, Lina rassemble tout le monde à l\'hôtel.\n— "L\'adversaire en demi-finale est Kirkwood. Axel les connaît."\n\nAxel se lève, inhabituellement sérieux.\n— "Ils ont une technique combinée — le Triangle Z des triplés Murdock — que personne n\'a jamais pu arrêter. Personne, jusqu\'à aujourd\'hui."\n\nIl marque une pause.\n— "Mark peut l\'arrêter. La Main Céleste peut tout arrêter si le timing est parfait."',
FALSE, NULL, 'public/img/Page6B.jpg', 'Victoire vs Terria. Demi-finale vs Kirkwood.', 'histoire'),

(38, 'Demi-finale nationale — Kirkwood',
'Mark réunit l\'équipe avant le match contre Kirkwood.',
FALSE, NULL, 'public/img/Page5.jpg', 'Demi-finale nationale contre Kirkwood.', 'dialogue'),

(39, 'Match contre Kirkwood',
'C\'est le match le plus difficile de votre parcours.\n\nKirkwood prend l\'avantage. Le Triangle Z frappe. 0-2.\n\nRaimon est dos au mur. Mais ils ne s\'effondrent pas.\n\nErik Eagle entre en jeu au moment crucial. Son Tri-Pégase réduit l\'écart. 1-2.\n\nAxel marque d\'une Tornade de Feu surpuissante. 2-2.\n\nEt dans les arrêts de jeu, Erik Eagle déclenche le Phénix. 3-2.',
FALSE, NULL, 'public/img/Page5.jpg', 'Match contre Kirkwood. Victoire épique 3-2.', 'match'),

(40, 'Victoire épique contre Kirkwood',
'3-2. La plus belle victoire du parcours.\n\nLe stade entier est debout. Même les supporters de Kirkwood applaudissent.\n\nNathan pleure dans les bras de Kevin. Mark s\'agenouille quelques secondes sur le gazon.\n\nJude range son carnet. Pour la première fois, il n\'analyse pas. Il profite juste du moment.\n\nEt toi, Takamura — tu réalises que ce voyage qui a commencé sur un quai de gare quelques semaines plus tôt t\'a mené ici. En finale nationale.\n\nContre Zeus Academy.',
FALSE, NULL, 'public/img/Page6B.jpg', 'Victoire 3-2 contre Kirkwood. Finale nationale !', 'histoire'),

(41, 'La veille de la finale',
'Tu croises Mark dans les couloirs de l\'hôtel, la nuit avant la grande finale.',
FALSE, NULL, 'public/img/Page5.jpg', 'La nuit avant la finale. Conversation avec Mark.', 'dialogue'),

(42, 'La Finale — Zeus Academy',
'Dans le tunnel du stade, Lina vous réunit avant Zeus Academy.',
FALSE, NULL, 'public/img/Page7.jpg', 'Finale nationale contre Zeus Academy.', 'dialogue'),

(43, 'Match — La Grande Finale',
'Zeus Academy est dans une autre dimension.\n\nDès la première minute, leurs attaquants créent des situations que votre défense n\'a jamais vues. Mark fait des arrêts extraordinaires — trois, quatre, cinq — mais il ne peut pas tenir indéfiniment.\n\nZeus marque. 0-1.\n\nRaimon ne s\'effondre pas. Tu es au cœur du jeu, Takamura. C\'est à toi de faire quelque chose.',
FALSE, NULL, 'public/img/Page7.jpg', 'Finale contre Zeus Academy.', 'match'),

(44, 'Football Frontier — Champion du Japon !',
'Le coup de sifflet final retentit.\n\nRaimon a gagné la finale nationale du Football Frontier.\n\nNathan explose dans un cri de joie. Kevin ferme les yeux un instant. Jude range son carnet avec un sourire.\n\nAxel marche vers toi et te serre la main.\n\nMark soulève le trophée sous les acclamations. Puis il te le tend.\n— "C\'est autant le tien que le nôtre, Takamura. Tu es arrivé ici comme un étranger. Tu repars comme un membre de cette famille."\n\nTu tiens le trophée entre tes mains. La foule scande le nom de Raimon.',
TRUE, 'victoire', 'public/img/FinVictoire.jpg', 'Victoire en finale nationale ! Champions du Japon !', 'histoire'),

(45, 'L\'Âme du Football Frontier',
'Dans les dernières secondes de la finale, quelque chose se produit.\n\nLe bandeau orange de Mark autour de ton front se serre. Une sensation monte dans tes jambes.\n\nTu prends ta course sans réfléchir. Ton corps sait ce qu\'il doit faire.\n\nUne rotation. Une frappe. Et le Cœur Flamboyant traverse les buts d\'Hector Hélio.\n\nRaimon gagne la finale.\n\nAprès le match, Mark s\'approche.\n— "Ce mouvement... mon grand-père me l\'avait montré une fois. Il disait que seul quelqu\'un avec une vraie âme de footballeur pouvait le reproduire."\n\nIl retire doucement le bandeau et le regarde longtemps.\n— "Il t\'appartenait depuis le début."',
TRUE, 'secrete', 'public/img/FinSecrete.jpg', 'Fin secrète : le Cœur Flamboyant.', 'histoire'),

(46, 'Ce n\'est que le début',
'Zeus Academy remporte la finale.\n\nTu restes assis sur le gazon. Les joueurs de Zeus célèbrent à l\'autre bout du terrain.\n\nMark s\'assoit à côté de toi. Il ne dit rien pendant plusieurs minutes.\n\nPuis :\n— "Le Football Frontier sera encore là l\'année prochaine, Takamura. Et nous aussi. Et la prochaine fois — on saura exactement pourquoi on se bat."\n\nIl te tend la main. Tu la prends.',
TRUE, 'defaite', 'public/img/FinDefaite.jpg', 'Défaite en finale nationale.', 'histoire'),

(47, 'Fin prématurée — Blessure',
'Tu t\'effondres sur le terrain. Une douleur fulgurante dans la cheville.\n\nEntorse sévère — ta saison est terminée.\n\nDe la touche, tu regardes tes coéquipiers se battre sans toi.\n\nQuand le coup de sifflet retentit, Mark vient te voir en premier.\n— "T\'as tout donné jusqu\'au bout. C\'est tout ce qu\'on peut demander."',
TRUE, 'defaite', 'public/img/FinDefaite.jpg', 'Blessure en cours de match.', 'histoire'),

(48, 'Expulsé — Mauvais choix',
'L\'arbitre sort le carton rouge.\n\nTu quittes le terrain sous les regards de tout le stade. Lina ne dit rien. C\'est pire que si elle avait crié.\n\nMark te regarde depuis les buts avec quelque chose qui n\'est pas de la colère. C\'est de la tristesse.',
TRUE, 'defaite', 'public/img/FinDefaite.jpg', 'Expulsion. Fin prématurée.', 'histoire');

-- ============================================================
-- CHOIX (un seul par couple source/cible — sans doublons)
-- ============================================================
INSERT INTO choix (id_page_source, id_page_cible, texte_bouton) VALUES
-- Ch1 navigation de base
(1,  2,  'Aller chercher ta salle de cours'),
(2,  3,  'S\'approcher des deux joueurs'),
-- Depuis page 4 : choisir un perso à aller voir
(4,  5,  'Aller parler à Axel Blaze'),
(4,  6,  'Trouver Jude Sharp'),
(4,  7,  'Chercher Nathan Swift'),
(4,  8,  'Rejoindre Kevin Dragonfly'),
-- Après match entraînement
(10, 11, 'Continuer la préparation'),
-- Chemins de préparation
(11, 12, 'S\'entraîner avec Mark Evans'),
(11, 13, 'Étudier la tactique avec Jude Sharp'),
(11, 14, 'Courir avec Nathan Swift'),
(11, 15, 'S\'entraîner seul'),
-- Vers le match amical
(12, 16, 'Le match amical approche'),
(13, 16, 'Le match amical approche'),
(14, 16, 'Le match amical approche'),
(15, 16, 'Le match amical approche'),
-- Après défaite amicale
(17, 18, 'Rejoindre les coéquipiers dans le vestiaire'),
-- Qualifications
(19, 20, 'Coup d\'envoi — Match contre Wild'),
-- Victoires successives
(21, 22, 'Préparer le deuxième tour'),
(24, 25, 'Se préparer pour la demi-finale Kantō'),
(25, 26, 'Coup d\'envoi — Match contre Otaku'),
(27, 28, 'Se préparer pour la finale Kantō'),
(30, 31, 'Cap sur le tournoi national'),
-- National
(31, 32, 'Affronter Shuriken au premier tour'),
(32, 33, 'Coup d\'envoi — Match contre Shuriken'),
(34, 35, 'Préparer le deuxième tour national'),
(35, 36, 'Coup d\'envoi — Match contre Terria'),
(37, 38, 'Préparer la demi-finale nationale'),
(40, 41, 'La veille de la grande finale'),
-- Finale : actions risquées
(43, 47, '[Dangereux] Provoquer l\'adversaire'),
(43, 48, '[Très Dangereux] Tacler violemment');

-- ============================================================
-- DIALOGUES
-- IDs auto-incrémentés dans l'ordre d'insertion :
-- Page 3  → id 1, 2      (dernier = 2)
-- Page 5  → id 3, 4      (dernier = 4)
-- Page 6  → id 5, 6      (dernier = 6)
-- Page 7  → id 7, 8      (dernier = 8)
-- Page 8  → id 9, 10     (dernier = 10)
-- Page 18 → id 11, 12, 13 (dernier = 13)
-- Page 22 → id 14, 15    (dernier = 15)
-- Page 28 → id 16, 17    (dernier = 17)
-- Page 38 → id 18, 19    (dernier = 19)
-- Page 41 → id 20, 21    (dernier = 21)
-- Page 42 → id 22, 23    (dernier = 23)
-- ============================================================

-- Page 3 : Mark Evans (id_personnage=1)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(3, 1, 'T\'es Takamura, le nouveau transfert ? On t\'attendait. Je suis Mark Evans, capitaine de Raimon.', 1, 'normal'),
(3, 1, 'Le Football Frontier, ça veut dire qu\'on va jouer contre les meilleures équipes du Japon. Si tu rejoins Raimon, je te promets une chose : tu ne regretteras jamais. Mais ça demande tout donner. Toujours. Alors — t\'es partant ?', 2, 'content');

-- Page 5 : Axel (id_personnage=2)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(5, 2, 'T\'es nouveau. Ça veut dire que tu as encore des mauvaises habitudes à perdre. Le foot ici, c\'est pas un jeu.', 1, 'normal'),
(5, 2, 'Si t\'as décidé de rester — alors travaille. Montre-moi que ta place ici est méritée. Je fais pas de cadeau, mais je respecte ceux qui bossent vraiment.', 2, 'normal');

-- Page 6 : Jude (id_personnage=3)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(6, 3, 'Je t\'observais cet après-midi. Tu as de bonnes jambes mais ta lecture du jeu est trop lente. Tu regardes le ballon au lieu de regarder l\'espace.', 1, 'normal'),
(6, 3, 'Le football, c\'est une question d\'anticipation. Quand tu cours vers le ballon, tu dois déjà savoir où il sera dans trois secondes. C\'est ça qui fait la différence entre un bon joueur et un grand joueur. Montre-moi que tu comprends ça.', 2, 'normal');

-- Page 7 : Nathan (id_personnage=4)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(7, 4, 'Alors, comment tu trouves Raimon ? C\'est génial ici, non ? L\'équipe est au top, le terrain est parfait, et on va gagner le Football Frontier cette année, j\'en suis sûr à 100% !', 1, 'content'),
(7, 4, 'T\'as bien fait de venir. On est une vraie équipe ici, Takamura. Et avec toi, on va être encore meilleurs. Bienvenue dans la famille !', 2, 'content');

-- Page 8 : Kevin (id_personnage=5)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(8, 5, 'T\'as du potentiel. Je l\'ai vu cet après-midi.', 1, 'normal'),
(8, 5, 'Ici, on joue pas pour gagner. On joue pour ne pas perdre ce qui compte. Si tu comprends ça — vraiment — alors tu seras un vrai joueur de Raimon.', 2, 'normal');

-- Page 18 : Après défaite (id_personnage=1, 5, 3)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(18, 1, 'On méritait ça. On était pas prêts. Mais maintenant on sait ce qu\'on doit faire.', 1, 'triste'),
(18, 5, 'Leur défenseur gauche anticipe trop tôt. Et leur gardien plonge en retard sur les frappes rasantes. J\'ai tout vu.', 2, 'normal'),
(18, 3, 'J\'ai tout analysé. On peut les battre au Football Frontier. Mais il faut jouer vraiment ensemble. Pas cinq joueurs — une équipe.', 3, 'normal');

-- Page 22 : Avant Cybertech (id_personnage=6, 3)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(22, 6, 'Cybertech joue en automates. Chaque mouvement est prévisible si vous savez quoi regarder. Jude, tu diriges le milieu.', 1, 'normal'),
(22, 3, 'J\'ai étudié leur système. Il y a une faille dans leur pressing. Je sais exactement quoi faire. Faites-moi confiance.', 2, 'content');

-- Page 28 : Avant finale Kantō (id_personnage=6, 2)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(28, 6, 'Ils ont changé leur système depuis le match amical. Ils savent ce qu\'on peut faire. Alors on va faire quelque chose qu\'ils n\'ont pas vu.', 1, 'normal'),
(28, 2, 'J\'en ai marre de perdre contre eux. Cette fois, c\'est différent. Cette fois, on les bat.', 2, 'colere');

-- Page 38 : Avant Kirkwood (id_personnage=1)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(38, 1, 'Quoi qu\'il arrive aujourd\'hui, je veux que vous sachiez que je suis fier de cette équipe. On a parcouru un chemin incroyable.', 1, 'content'),
(38, 1, 'Maintenant — on va jusqu\'au bout. Pour Raimon. Pour nous. Pour tout ce qu\'on a construit ensemble.', 2, 'content');

-- Page 41 : Veille finale (id_personnage=1)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(41, 1, 'Mon grand-père m\'a dit : "Un match ne se gagne pas avec les jambes. Il se gagne avec ce qui ne se voit pas. L\'invisible."', 1, 'normal'),
(41, 1, 'Quoi qu\'il arrive demain, Takamura — tu fais partie de cette équipe. Et cette équipe, c\'est ma famille.', 2, 'content');

-- Page 42 : Avant finale Zeus (id_personnage=6)
INSERT INTO dialogue (id_page, id_personnage, texte, ordre, expression) VALUES
(42, 6, 'Tout ce que vous avez vécu depuis le début de cette aventure — chaque entraînement, chaque défaite, chaque victoire — c\'est pour ce moment.', 1, 'normal'),
(42, 6, 'Zeus Academy est forte. La plus forte qu\'on ait jamais affrontée. Mais vous aussi, vous avez changé. Vous n\'êtes plus les mêmes joueurs qu\'au début. Allez les gagner.', 2, 'content');

-- ============================================================
-- RÉPONSES DIALOGUE — liées aux bons derniers id_dialogue
-- ============================================================

-- Page 3 → dernier dialogue id=2 → suivante page 4
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(2, '"Oui. Je suis partant. À fond."',                           1, '{"courage":1,"leadership":1}',  15, 4),
(2, '"Je suis partant, mais je veux d\'abord voir l\'équipe."', 2, '{"technique":1}',               10, 4),
(2, '"Honnêtement... j\'ai peur de ne pas être à la hauteur."', 3, '{"chance":1}',                  -5, 4),
(2, '"Non. Je préfère rester dans l\'ombre pour l\'instant."',  4, '{"stamina":-1}',               -15, 4);

-- Page 5 → dernier dialogue id=4 → suivante page 9
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(4, '"Je suis là pour travailler. Je vais te le montrer."', 1, '{"technique":1}', 15, 9),
(4, '"J\'entends ce que tu dis. Je vais progresser."',      2, '{"courage":1}',    5, 9),
(4, '"Tu es dur... mais tu as probablement raison."',       3, '{"stamina":1}',    0, 9);

-- Page 6 → dernier dialogue id=6 → suivante page 9
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(6, '"Tu as raison. Je vais changer ma façon de voir le jeu."', 1, '{"technique":1,"leadership":1}', 20, 9),
(6, '"C\'est plus facile à dire qu\'à faire..."',               2, '{"chance":1}',                    5, 9),
(6, '"Je comprends. Montre-moi comment faire."',               3, '{"technique":1}',                10, 9);

-- Page 7 → dernier dialogue id=8 → suivante page 9
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(8, '"Merci Nathan ! Je suis content d\'être là !"',    1, '{"courage":1,"stamina":1}', 20, 9),
(8, '"T\'es vraiment enthousiaste ! Ça fait du bien."', 2, '{"vitesse":1}',             10, 9),
(8, '"Je vais faire mon possible."',                    3, '{"chance":1}',               5, 9);

-- Page 8 → dernier dialogue id=10 → suivante page 9
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(10, '"Je comprends. Je vais jouer pour ce qui compte."', 1, '{"leadership":1,"courage":1}', 20, 9),
(10, '"C\'est une belle façon de voir les choses."',      2, '{"technique":1}',              10, 9),
(10, '"Je vais y réfléchir."',                            3, '{"chance":1}',                  5, 9);

-- Page 18 → dernier dialogue id=13 → suivante page 19
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(13, '"On va les battre au Football Frontier. J\'en suis sûr."', 1, '{"courage":1}',               10, 19),
(13, '"Cette défaite m\'a appris quelque chose d\'important."',  2, '{"technique":1,"vitesse":1}',  10, 19),
(13, '"On a encore du chemin à faire..."',                       3, '{"stamina":1}',                -5, 19),
(13, '"Je me demande si on est vraiment capables de gagner."',   4, '{"stamina":-1}',              -15, 19);

-- Page 22 → dernier dialogue id=15 → suivante page 23
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(15, '"Compris. Je suis prêt à tout donner."',         1, '{"courage":1}',    10, 23),
(15, '"Quelle est ma mission précise dans ce plan ?"', 2, '{"technique":1}',  15, 23),
(15, '"Je fais confiance à l\'équipe."',               3, '{"leadership":1}',  5, 23);

-- Page 28 → dernier dialogue id=17 → suivante page 29
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(17, '"On les bat. Sans hésitation."',                  1, '{"courage":1}',               15, 29),
(17, '"Je me souviens de leur faille. Je suis prêt."',  2, '{"technique":1,"vitesse":1}', 10, 29),
(17, '"Cette fois c\'est différent. On a progressé."',  3, '{"leadership":1}',            10, 29);

-- Page 38 → dernier dialogue id=19 → suivante page 39
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(19, '"On va jusqu\'au bout. Pour Raimon."',           1, '{"courage":1,"leadership":1}', 20, 39),
(19, '"Je suis fier d\'être dans cette équipe."',      2, '{"stamina":1}',                15, 39),
(19, '"On va gagner. Je le sens."',                    3, '{"chance":1}',                 10, 39);

-- Page 41 → dernier dialogue id=21 → suivante page 42
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(21, '"Merci Mark. Demain on joue pour cette famille."', 1, '{"courage":1,"leadership":1}', 25, 42),
(21, '"L\'invisible... je crois que je comprends."',     2, '{"technique":1}',              15, 42),
(21, '"Je vais pas te décevoir."',                       3, '{"courage":1}',                20, 42);

-- Page 42 → dernier dialogue id=23 → suivante page 43
INSERT INTO reponse_dialogue (id_dialogue, texte_bouton, ordre, effet_stats, effet_affinite, id_page_suivante) VALUES
(23, '"On est prêts. Allons-y."',                        1, '{"courage":1}',    10, 43),
(23, '"Zeus va découvrir ce qu\'est Raimon."',           2, '{"leadership":1}',  10, 43),
(23, '"Pour tout ce chemin parcouru — on gagne."',       3, '{"stamina":1}',     10, 43);

-- ============================================================
-- MATCHS
-- ============================================================
INSERT INTO match_config (id_page, nom_adversaire, image_adversaire, stats_adversaire, seuil_victoire, id_page_victoire, id_page_defaite, bonus_victoire, description) VALUES
(9,  'Groupe A — Raimon',  NULL,                              '{"technique":3,"vitesse":3,"defense":3,"leadership":2}', 2, 10, 10, '{"technique":1}',                        'Match d\'entraînement interne. Pas de pression — juste du jeu.'),
(20, 'Collège Wild',       'public/img/logos/wild.png',       '{"technique":2,"vitesse":2,"tir":2,"defense":2}',        2, 21, 47, '{"courage":1}',                          'Premier tour qualifications Kantō. Wild joue physique.'),
(23, 'Collège Cybertech',  'public/img/logos/cybertech.png',  '{"technique":3,"vitesse":2,"tir":3,"defense":3}',        3, 24, 47, '{"technique":1,"vitesse":1}',            'Deuxième tour qualifications. Cybertech joue de façon calculée.'),
(26, 'Collège Otaku',      'public/img/logos/otaku.png',      '{"technique":3,"vitesse":3,"tir":3,"defense":3}',        3, 27, 47, '{"courage":1,"leadership":1}',           'Demi-finale Kantō. Otaku est coriace et joue collectif.'),
(29, 'Royal Academy',      'public/img/logos/royal.png',      '{"technique":4,"vitesse":3,"tir":4,"defense":4}',        3, 30, 46, '{"courage":1,"technique":1}',            'Finale Kantō. La Royal Academy veut sa revanche.'),
(33, 'Shuriken',           'public/img/logos/shuriken.png',   '{"technique":3,"vitesse":4,"tir":3,"defense":3}',        3, 34, 47, '{"vitesse":1}',                          'Premier tour national. Shuriken joue sur la vitesse.'),
(36, 'Terria',             'public/img/logos/terria.png',     '{"technique":3,"vitesse":3,"tir":3,"defense":4}',        3, 37, 47, '{"stamina":1}',                          'Deuxième tour national. Terria joue avec une intensité forte.'),
(39, 'Kirkwood',           'public/img/logos/kirkwood.png',   '{"technique":4,"vitesse":4,"tir":4,"defense":4}',        4, 40, 46, '{"courage":1,"leadership":1}',           'Demi-finale nationale. Les triplés Murdock et leur Triangle Z.'),
(43, 'Zeus Academy',       'public/img/logos/zeus.png',       '{"technique":5,"vitesse":5,"tir":5,"defense":5}',        4, 44, 46, '{"courage":1,"technique":1,"leadership":1}', 'La Grande Finale. Zeus Academy n\'a jamais concédé un but.');

-- ============================================================
-- ACTIONS MATCH
-- ============================================================
INSERT INTO action_match (id_match, nom, type, stat_utilisee, points_succes, texte_succes, texte_echec) VALUES
-- Match entraînement (id_match=1)
(1,'Tenter un tir',        'tir',     'technique', 1, 'Ta frappe trouve le filet ! Beau but !',        'Le gardien arrête ton tir facilement.'),
(1,'Dribbler vers les buts','dribble','vitesse',   1, 'Tu passes deux défenseurs avec élégance !',     'Tu perds le ballon face au défenseur.'),
(1,'Passe en profondeur',  'passe',   'technique', 1, 'Passe parfaite, ton coéquipier marque !',       'La passe est interceptée.'),
(1,'Défendre sur le contre','defense','courage',   0, 'Tu bloques l\'attaque adverse proprement.',     'L\'attaquant te passe.'),
-- Wild (id_match=2)
(2,'Tir surpuissant',      'tir',     'technique', 1, 'BUT ! Le gardien de Wild n\'a rien pu faire !', 'Wild arrête le tir.'),
(2,'Sprint en couloir',    'dribble', 'vitesse',   1, 'Tu débordes sur l\'aile et centres parfaitement !', 'Le défenseur te rattrape.'),
(2,'Combinaison avec Axel','technique','leadership',1,'La combinaison fonctionne ! BUT !',             'La combinaison est mal synchronisée.'),
(2,'Bloc défensif',        'defense', 'courage',   0, 'Tu arrêtes l\'attaque de Wild.',                'Wild contre-attaque.'),
-- Cybertech (id_match=3)
(3,'Tornade du Dragon',    'technique','technique', 1, 'Axel et Kevin combinent ! BUT imparable !',    'Cybertech analyse et bloque la technique.'),
(3,'Frappe croisée',       'tir',     'technique', 1, 'Frappe précise dans le coin ! BUT !',           'Le gardien devine et s\'interpose.'),
(3,'Pressing collectif',   'defense', 'leadership',0, 'Vous récupérez le ballon haut sur le terrain !','Cybertech contourne votre pressing.'),
(3,'Sprint contre-attaque','dribble', 'vitesse',   1, 'Contre-attaque éclair ! BUT !',                 'Cybertech récupère et repart.'),
-- Otaku (id_match=4)
(4,'Choc du Dragon de Kevin','technique','technique',1,'Le Choc du Dragon perfore le gardien !',       'Otaku bloque la technique combinée.'),
(4,'Centre précis',        'passe',   'technique', 1, 'Ton centre trouve Kevin à la perfection !',     'Le centre est dévié en corner.'),
(4,'Blocage héroïque',     'defense', 'courage',   0, 'Tu bloques leur frappe avec le corps !',        'Otaku marque sur cette action.'),
(4,'Accélération',         'dribble', 'vitesse',   1, 'Tu prends le dessus sur ton vis-à-vis !',       'Tu perds le duel de vitesse.'),
-- Finale Kantō Royal Academy (id_match=5)
(5,'Tornade de Feu d\'Axel','tir',    'technique', 1, 'La Tornade de Feu est imparable ! 1-1 !',       'Royal Academy bloque avec une technique inconnue.'),
(5,'Super Trampoline du Tonnerre','technique','leadership',1,'Axel et Mark combinent ! 2-1 !',          'La combinaison échoue sous la pression.'),
(5,'Pressing intensif',    'defense', 'stamina',   0, 'Vous récupérez le ballon sur leur relance.',    'Royal Academy sort proprement du pressing.'),
(5,'Contre-attaque rapide','dribble', 'vitesse',   1, 'La contre-attaque surprend la Royal Academy !', 'La Royal Academy récupère et attaque.'),
-- Shuriken (id_match=6)
(6,'Tornade de Feu',       'tir',     'technique', 1, 'Axel déclenche sa Tornade de Feu ! BUT !',      'Le gardien de Shuriken réalise un arrêt spectaculaire.'),
(6,'Oiseau de Feu de Nathan','technique','vitesse', 1, 'Nathan s\'envole et marque de l\'Oiseau de Feu !','Shuriken lit la technique et bloque.'),
(6,'Blocage défensif',     'defense', 'courage',   0, 'Tu empêches Shuriken de marquer.',              'Shuriken contre-attaque.'),
(6,'Dribble ninja',        'dribble', 'vitesse',   1, 'Tu les bats à leur propre jeu de vitesse !',    'Shuriken te dépasse avec leur technique.'),
-- Terria (id_match=7)
(7,'Éclair Pulvérisant de Jude','technique','technique',1,'Jude déclenche l\'Éclair Pulvérisant ! BUT !','Terria résiste à la technique de Jude.'),
(7,'Frappe rasante',       'tir',     'technique', 1, 'Ta frappe rasante trompe le gardien !',          'Le gardien de Terria plonge et arrête.'),
(7,'Solidarité défensive', 'defense', 'leadership',0, 'L\'équipe tient bon face à Terria.',             'Terria perce votre défense.'),
(7,'Sprint surprise',      'dribble', 'vitesse',   1, 'Tu prends Terria de vitesse !',                  'Terria t\'anticipe et récupère.'),
-- Kirkwood (id_match=8)
(8,'Tri-Pégase d\'Erik',   'technique','technique',1, 'Erik Eagle déclenche le Tri-Pégase ! 1-2 !',    'Kirkwood résiste à la technique.'),
(8,'Phénix d\'Erik',       'tir',     'courage',   1, 'Le Phénix transperce le gardien ! 3-2 !',        'Kirkwood bloque avec une technique spectaculaire.'),
(8,'Bloc du Triangle Z',   'defense', 'courage',   0, 'Mark arrête le Triangle Z ! Extraordinaire !',  'Le Triangle Z est imparable. Kirkwood marque.'),
(8,'Frappe de loin',       'tir',     'technique', 1, 'Ta frappe de loin surprend Kirkwood !',          'Ta frappe passe au-dessus des buts.'),
-- Finale Zeus (id_match=9)
(9,'Cœur Flamboyant',      'technique','courage',  1, 'Une technique inconnue explose dans les buts de Zeus !','Ta technique mystérieuse échoue face à Zeus.'),
(9,'Tornade de Feu',       'tir',     'technique', 1, 'La Tornade de Feu d\'Axel trouve le filet !',   'Hector Hélio arrête la Tornade avec sa Main Céleste X.'),
(9,'Bloc héroïque de Mark','defense', 'leadership',0, 'Mark arrête l\'impossible. Le stade explose.',  'Zeus perce les défenses. Mark ne peut rien faire.'),
(9,'Sprint décisif',       'dribble', 'vitesse',   1, 'Tu prends Zeus à contrepied dans un sprint parfait !','Zeus est trop rapide. Tu perds le ballon.');

-- ============================================================
-- SCÈNE SECRÈTE
-- ============================================================
INSERT INTO scene_secrete (id_page, id_page_secrete, condition_json, description) VALUES
(43, 45, '{"courage_min":3,"objet_requis":1}', 'Fin secrète : Cœur Flamboyant si bandeau Mark + courage >= 3');

-- ============================================================
-- ÉVÉNEMENTS ALÉATOIRES
-- ============================================================
INSERT INTO evenement_aleatoire (id_page, proba_base, effet_json, texte_declenchement) VALUES
(4,  20, '{"stamina":1}',    'Tu trouves une boisson isotonique dans ton sac. Stamina +1 !'),
(11, 25, '{"courage":1}',    'Des supporters de Raimon passent et t\'encouragent. Courage +1 !'),
(19, 20, '{"chance":1}',     'Tu trouves un talisman porte-bonheur avant le match. Chance +1 !'),
(31, 15, '{"vitesse":1}',    'L\'adrénaline du tournoi national te booste. Vitesse +1 !'),
(40, 20, '{"leadership":1}', 'Après Kirkwood, l\'équipe te regarde comme un leader. Leadership +1 !'),
(43, 15, '{"courage":1}',    'Dans le tunnel, Mark pose la main sur ton épaule. "T\'es prêt." Courage +1 !');

-- ============================================================
-- VÉRIFICATION
-- ============================================================
SELECT 'BDD Football Frontier installée avec succès !' AS statut;
SELECT COUNT(*) AS nb_pages         FROM page;
SELECT COUNT(*) AS nb_choix         FROM choix;
SELECT COUNT(*) AS nb_personnages   FROM personnage;
SELECT COUNT(*) AS nb_dialogues     FROM dialogue;
SELECT COUNT(*) AS nb_reponses      FROM reponse_dialogue;
SELECT COUNT(*) AS nb_matchs        FROM match_config;
SELECT COUNT(*) AS nb_actions_match FROM action_match;

-- Vérif réponses par page dialogue
SELECT d.id_page, MAX(d.id_dialogue) AS dernier_dial, COUNT(r.id_reponse) AS nb_reponses
FROM dialogue d
LEFT JOIN reponse_dialogue r ON r.id_dialogue = d.id_dialogue
WHERE d.id_dialogue IN (SELECT MAX(id_dialogue) FROM dialogue GROUP BY id_page)
GROUP BY d.id_page ORDER BY d.id_page;

SET FOREIGN_KEY_CHECKS = 1;



UPDATE page SET image = 'public/img/Page1.jpg' WHERE id_page = 1;
UPDATE page SET image = 'public/img/Page2.jpg' WHERE id_page = 2;
UPDATE page SET image = 'public/img/Page3.jpg' WHERE id_page = 3;
UPDATE page SET image = 'public/img/Page4.jpg' WHERE id_page = 4;
UPDATE page SET image = 'public/img/Page5.jpg' WHERE id_page = 5;
UPDATE page SET image = 'public/img/Page6.jpg' WHERE id_page = 6;
UPDATE page SET image = 'public/img/Page7.png' WHERE id_page = 7;
UPDATE page SET image = 'public/img/Page8.jpg' WHERE id_page = 8;
UPDATE page SET image = 'public/img/Page9.jpg' WHERE id_page = 9;
UPDATE page SET image = 'public/img/Page10.jpg' WHERE id_page = 10;
UPDATE page SET image = 'public/img/Page11.jpg' WHERE id_page = 11;
UPDATE page SET image = 'public/img/Page12.jpg' WHERE id_page = 12;
UPDATE page SET image = 'public/img/Page13.jpg' WHERE id_page = 13;
UPDATE page SET image = 'public/img/Page14.png' WHERE id_page = 14;
UPDATE page SET image = 'public/img/Page15.png' WHERE id_page = 15;
UPDATE page SET image = 'public/img/Page16.jpg' WHERE id_page = 16;
UPDATE page SET image = 'public/img/Page17.jpg' WHERE id_page = 17;
UPDATE page SET image = 'public/img/Page18.jpg' WHERE id_page = 18;
UPDATE page SET image = 'public/img/Page19.jpg' WHERE id_page = 19;
UPDATE page SET image = 'public/img/Page20.jpg' WHERE id_page = 20;
UPDATE page SET image = 'public/img/Page21.jpg' WHERE id_page = 21;
UPDATE page SET image = 'public/img/Page22.png' WHERE id_page = 22;
UPDATE page SET image = 'public/img/Page23.jpg' WHERE id_page = 23;
UPDATE page SET image = 'public/img/Page24.jpg' WHERE id_page = 24;
UPDATE page SET image = 'public/img/Page25.jpg' WHERE id_page = 25;
UPDATE page SET image = 'public/img/Page26.jpg' WHERE id_page = 26;
UPDATE page SET image = 'public/img/Page27.jpg' WHERE id_page = 27;
UPDATE page SET image = 'public/img/Page28.jpg' WHERE id_page = 28;
UPDATE page SET image = 'public/img/Page29.jpg' WHERE id_page = 29;
UPDATE page SET image = 'public/img/Page30.jpg' WHERE id_page = 30;
UPDATE page SET image = 'public/img/Page31.jpg' WHERE id_page = 31;
UPDATE page SET image = 'public/img/Page32.jpg' WHERE id_page = 32;
UPDATE page SET image = 'public/img/Page33.jpg' WHERE id_page = 33;
UPDATE page SET image = 'public/img/Page34.jpg' WHERE id_page = 34;
UPDATE page SET image = 'public/img/Page35.png' WHERE id_page = 35;
UPDATE page SET image = 'public/img/Page36.jpg' WHERE id_page = 36;
UPDATE page SET image = 'public/img/Page37.jpg' WHERE id_page = 37;
UPDATE page SET image = 'public/img/Page38.jpg' WHERE id_page = 38;
UPDATE page SET image = 'public/img/Page39.jpg' WHERE id_page = 39;
UPDATE page SET image = 'public/img/Page40.jpg' WHERE id_page = 40;
UPDATE page SET image = 'public/img/Page41.jpg' WHERE id_page = 41;
UPDATE page SET image = 'public/img/Page42.png' WHERE id_page = 42;
UPDATE page SET image = 'public/img/Page43.jpg' WHERE id_page = 43;

-- Fins
UPDATE page SET image = 'public/img/FinVictoire.jpg' WHERE id_page = 44;
UPDATE page SET image = 'public/img/FinSecrete.jpg'  WHERE id_page = 45;
UPDATE page SET image = 'public/img/FinDefaite.jpg'  WHERE id_page = 46;

-- Pages d'erreur / expulsion
UPDATE page SET image = 'public/img/Page47.jpg' WHERE id_page = 47;
UPDATE page SET image = 'public/img/Page48.jpg' WHERE id_page = 48;

-- Vérif rapide
SELECT id_page, titre, image FROM page ORDER BY id_page;

INSERT IGNORE INTO choix (id_page_source, id_page_cible, texte_bouton) VALUES
(16, 17, 'Entrer sur le terrain — Match amical vs Royal Academy');