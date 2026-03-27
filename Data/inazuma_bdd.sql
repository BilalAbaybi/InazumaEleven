-- ============================================================
--  BASE DE DONNÉES - "Football Frontier : Le Nouveau de Raimon"
--  Projet BTS SIO - Lycée Fulbert
--  Technologies : MariaDB
-- ============================================================

CREATE DATABASE IF NOT EXISTS football_frontier CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE football_frontier;

-- ------------------------------------------------------------
-- TABLE : page
-- Contient toutes les pages/scènes de l'histoire
-- ------------------------------------------------------------
CREATE TABLE page (
    id_page       INT AUTO_INCREMENT PRIMARY KEY,
    titre         VARCHAR(100)  NOT NULL,
    texte         TEXT          NOT NULL,
    est_fin       BOOLEAN       DEFAULT FALSE,
    type_fin      VARCHAR(20)   DEFAULT NULL,   -- 'victoire', 'defaite', 'secrete', NULL
    image         VARCHAR(200)  DEFAULT NULL    -- chemin vers l'image illustrant la page
);

-- ------------------------------------------------------------
-- TABLE : objet
-- Objets que le joueur peut récupérer au fil de l'histoire
-- ------------------------------------------------------------
CREATE TABLE objet (
    id_objet         INT AUTO_INCREMENT PRIMARY KEY,
    nom              VARCHAR(100) NOT NULL,
    description      TEXT         NOT NULL,
    effet_courage    INT          DEFAULT 0,   -- bonus appliqué à la stat courage
    effet_technique  INT          DEFAULT 0,   -- bonus appliqué à la stat technique
    effet_stamina    INT          DEFAULT 0    -- bonus appliqué à la stat stamina
);

-- ------------------------------------------------------------
-- TABLE : choix
-- Liens entre pages avec conditions d'affichage
-- ------------------------------------------------------------
CREATE TABLE choix (
    id_choix           INT AUTO_INCREMENT PRIMARY KEY,
    id_page_source     INT          NOT NULL,
    id_page_cible      INT          NOT NULL,
    texte_bouton       VARCHAR(150) NOT NULL,
    cond_courage_min   INT          DEFAULT 0,   -- courage minimum requis pour voir ce choix
    cond_technique_min INT          DEFAULT 0,   -- technique minimum requise
    cond_stamina_min   INT          DEFAULT 0,   -- stamina minimum requise
    cond_objet_requis  INT          DEFAULT NULL, -- id_objet requis (NULL = pas de condition)
    FOREIGN KEY (id_page_source)    REFERENCES page(id_page),
    FOREIGN KEY (id_page_cible)     REFERENCES page(id_page),
    FOREIGN KEY (cond_objet_requis) REFERENCES objet(id_objet)
);

-- ------------------------------------------------------------
-- TABLE : partie
-- Une partie par joueur, stocke l'état courant
-- ------------------------------------------------------------
CREATE TABLE partie (
    id_partie      INT AUTO_INCREMENT PRIMARY KEY,
    pseudo         VARCHAR(50)  NOT NULL,
    date_debut     DATETIME     DEFAULT NOW(),
    page_actuelle  INT          NOT NULL DEFAULT 1,
    terminee       BOOLEAN      DEFAULT FALSE,
    fin_obtenue    VARCHAR(20)  DEFAULT NULL,   -- 'victoire', 'defaite', 'secrete'
    nb_pages_vues  INT          DEFAULT 0,
    FOREIGN KEY (page_actuelle) REFERENCES page(id_page)
);

-- ------------------------------------------------------------
-- TABLE : stats_joueur
-- Stats Courage / Technique / Stamina liées à une partie
-- ------------------------------------------------------------
CREATE TABLE stats_joueur (
    id_stats   INT AUTO_INCREMENT PRIMARY KEY,
    id_partie  INT NOT NULL UNIQUE,             -- 1 seule ligne de stats par partie
    courage    INT DEFAULT 1 CHECK (courage  BETWEEN 0 AND 3),
    technique  INT DEFAULT 1 CHECK (technique BETWEEN 0 AND 3),
    stamina    INT DEFAULT 2 CHECK (stamina  BETWEEN 0 AND 3),
    FOREIGN KEY (id_partie) REFERENCES partie(id_partie)
);

-- ------------------------------------------------------------
-- TABLE : inventaire_joueur
-- Objets collectés par le joueur au fil de la partie
-- ------------------------------------------------------------
CREATE TABLE inventaire_joueur (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    id_partie        INT      NOT NULL,
    id_objet         INT      NOT NULL,
    date_obtention   DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_partie) REFERENCES partie(id_partie),
    FOREIGN KEY (id_objet)  REFERENCES objet(id_objet)
);

-- ------------------------------------------------------------
-- TABLE : historique_choix
-- Enregistre chaque choix fait par le joueur (empêche le retour arrière)
-- ------------------------------------------------------------
CREATE TABLE historique_choix (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    id_partie   INT      NOT NULL,
    id_choix    INT      NOT NULL,
    date_choix  DATETIME DEFAULT NOW(),
    FOREIGN KEY (id_partie) REFERENCES partie(id_partie),
    FOREIGN KEY (id_choix)  REFERENCES choix(id_choix)
);

-- ============================================================
-- DONNÉES : Objets du jeu
-- ============================================================
INSERT INTO objet (nom, description, effet_courage, effet_technique, effet_stamina) VALUES
('Bandeau de Mark Evans',
 'Le vieux bandeau orange de Mark, celui qu il portait lors de sa première victoire au Football Frontier. Il est écrit dessus : "Ne lâche jamais — M.E."',
 1, 0, 0),

('Crampons d Axel Blaze',
 'Les crampons avec lesquels Axel a déclenché sa première Tornade de Feu officielle. Ils te vont parfaitement.',
 0, 1, 0),

('Carnet de Jude Sharp',
 'Rempli de formations secrètes et de notes en rouge. "Formation pour contrer la Royal Academy — ne pas divulguer."',
 0, 1, 0),

('Boisson isotonique',
 'Une boisson de récupération trouvée dans le vestiaire. Récupère 1 point de Stamina.',
 0, 0, 1);

-- ============================================================
-- DONNÉES : Pages de l'histoire
-- ============================================================
INSERT INTO page (titre, texte, est_fin, type_fin) VALUES

-- Page 1 : Accueil
('Football Frontier — Le Nouveau de Raimon',
 'Le Football Frontier. Le tournoi le plus intense du Japon. Raimon y croit depuis toujours. Et cette année... tu en fais partie. Tu es Ryu Takamura, lycéen transféré au lycée Raimon en plein milieu du tournoi. Tout commence maintenant.',
 FALSE, NULL),

-- Page 2 : Rencontre avec Mark Evans
('Le terrain de Raimon',
 'C est la pause déjeuner. Tu regardes l équipe de Raimon s entraîner depuis les gradins. Mark Evans arrête un tir de pleine puissance d Axel Blaze avec sa légendaire Main Céleste, puis te remarque dans les tribunes. "Hé, toi ! T as l œil pour le foot. On a besoin d un joueur pour le Football Frontier. T es partant ?" Axel Blaze croise les bras sans rien dire. Jude Sharp ajuste ses lunettes et t observe, impassible.',
 FALSE, NULL),

-- Page 3A : Entraînement avec Raimon
('Entraînement avec Raimon',
 'Mark t intègre à l équipe immédiatement. Axel Blaze te montre les bases de sa Tornade de Feu. Kevin Dragonfly t explique l élan du Choc du Dragon. Jude Sharp observe et ne dit rien jusqu à la fin : "Tu as des jambes. Le reste, ça se travaille." Après l entraînement, dans le vestiaire, tu aperçois deux objets oubliés là.',
 FALSE, NULL),

-- Page 3B : Entraînement solo
('Entraînement solitaire',
 'Tu t entraînes seul sur un terrain vague jusqu à la tombée de la nuit. En rentrant, tu passes devant la salle de tactique de Raimon. La porte est entrouverte. Sur le bureau traîne le carnet de stratégies de Jude Sharp, couvert de formations et de notes en rouge.',
 FALSE, NULL),

-- Page 4A : Nuit avec bandeau
('La nuit avant le match — Bandeau de Mark',
 'Tu tiens le bandeau orange de Mark dans tes mains. Il a griffonné dessus : "Ne lâche jamais — M.E." Tu le serres et tu t endors. Le lendemain matin, Mark te tape sur l épaule : "Je savais que t étais là depuis le début, Takamura." Il ne te demande pas où tu as trouvé son bandeau.',
 FALSE, NULL),

-- Page 4B : Nuit avec crampons
('La nuit avant le match — Crampons d Axel',
 'Les crampons d Axel Blaze. Ceux avec lesquels il a déclenché sa première Tornade de Feu au Football Frontier. Tu les enfiles. Ils te vont parfaitement. Le lendemain, Axel te voit les porter avant le match. Il détourne le regard, puis murmure : "Fais-en bon usage."',
 FALSE, NULL),

-- Page 4C : Carnet de Jude
('La nuit avant le match — Carnet de Jude Sharp',
 'Tu parcours le carnet toute la nuit. Des formations en diamant, des contre-attaques chirurgicales, une note encadrée : "Le Triangle de la Mort ne fonctionne que si le gardien panique. Ne pas utiliser contre Mark Evans." Le lendemain, Jude arrive et voit son carnet déplacé. Il te regarde trois secondes. Puis hoche la tête, une seule fois.',
 FALSE, NULL),

-- Page 4D : Rien pris
('La nuit avant le match — Les mains vides',
 'Tu rentres sans rien. La nuit est interminable. Tu repenses au regard d Axel Blaze, aux stratégies de Jude Sharp, à la Main Céleste de Mark. Et toi là-dedans, tu es qui ? La demi-finale contre la Royal Academy commence dans quelques heures.',
 FALSE, NULL),

-- Page 5 : Demi-finale
('Demi-finale : Raimon vs Royal Academy',
 'Les gradins sont bondés. La Royal Academy mène 0-1 à la mi-temps. Dans le vestiaire, Mark Evans est debout face à l équipe : "La Royal Academy pense qu on va baisser les bras. Prouvons-leur qu ils ont tout faux. Takamura, c est ton moment." Nathan Swift te passe le brassard. Axel Blaze te regarde sans rien dire. Kevin Dragonfly te fait un signe de tête. Tu as 45 minutes.',
 FALSE, NULL),

-- Page 6A : Tornade de Feu (succès)
('La Tornade de Feu — Succès',
 'Tu prends ta course. Tu repenses aux gestes d Axel. L élan, la rotation, le pied gauche. Une flamme orange explose autour du ballon. Le gardien de la Royal Academy reste figé. 1-1 ! Dans les tribunes, quelqu un crie : "C est une Tornade de Feu !" Axel, sur le banc, ne dit rien — mais il sourit.',
 FALSE, NULL),

-- Page 6A bis : Tornade de Feu (sans crampons)
('La Tornade de Feu — Rebond',
 'Tu prends ta course. Le tir part trop court, ricoche sur le poteau. Kevin Dragonfly, en embuscade, reprend d un Choc du Dragon. But ! 1-1, mais c est lui le héros. Tu gardes la tête baissée.',
 FALSE, NULL),

-- Page 6B : Jeu collectif
('Jeu collectif',
 'Tu fais une remise dos au but, tu élimines ton défenseur d un crochet et tu sers Axel Blaze sur le côté gauche. Il n a pas besoin d un mot. Il déclenche sa Tornade de Feu. Imparable. 1-1 ! Jude Sharp te rejoint : "Bonne lecture du jeu." De la part de Jude Sharp, c est un discours.',
 FALSE, NULL),

-- Page 6C : Sacrifice
('Le sacrifice',
 'Tu décroches pour couvrir Jude Sharp. Tu bloques un contre de la Royal Academy en plein sprint — le ballon te percute le tibia. Tu restes au sol quelques secondes. Mark s agenouille : "T as sauvé l équipe, Takamura." Jude lance Nathan Swift dans la profondeur, qui centre pour Axel : Tornade de Feu. 1-1.',
 FALSE, NULL),

-- Page 7 : Finale
('La Finale : Raimon vs Zeus Academy',
 'Vous avez éliminé la Royal Academy. La finale du Football Frontier vous attend. L adversaire : Zeus Academy, une équipe entraînée dans des conditions surhumaines. Score : 1-1, à 5 minutes de la fin. Mark Evans te crie depuis les buts : "Tout ce qu on a vécu depuis le début du Football Frontier — c est pour ce moment. Lâche tout, Takamura !"',
 FALSE, NULL),

-- Fin Victoire
('Football Frontier — Champion !',
 'Le coup de sifflet final retentit. Raimon a gagné le Football Frontier. Mark Evans soulève le trophée sous les cris des gradins. Il te le tend : "C est autant le tien que le nôtre, Takamura." Axel Blaze hoche la tête. Kevin Dragonfly lève le poing. Jude Sharp regarde ailleurs — mais il sourit.',
 TRUE, 'victoire'),

-- Fin Secrète
('L Âme du Football Frontier',
 'Dans les dernières secondes, le bandeau orange de Mark autour de ton front se serre. Une sensation monte dans tes jambes — quelque chose que personne ne t a appris. Tu déclenches le Cœur Flamboyant, une rotation enflammée entre la Tornade de Feu et quelque chose d entièrement nouveau. Le gardien de Zeus Academy n a aucune réaction. Après le match, Mark s approche : "Ce mouvement... mon grand-père me l avait montré une fois. Comment tu..." Il ne finit pas. Il enlève le bandeau de ta tête doucement et le regarde longtemps.',
 TRUE, 'secrete'),

-- Fin Défaite
('Ce n est que le début',
 'Tu quittes le terrain sous les regards de Zeus Academy qui célèbre. Dans les gradins vides, Mark Evans vient s asseoir à côté de toi sans un mot. Après un long silence : "Le Football Frontier sera encore là l année prochaine. Et toi aussi." Il te tend la main.',
 TRUE, 'defaite');

-- ============================================================
-- DONNÉES : Choix entre les pages
-- ============================================================
INSERT INTO choix (id_page_source, id_page_cible, texte_bouton, cond_courage_min, cond_technique_min, cond_stamina_min, cond_objet_requis) VALUES

-- Depuis page 1 (Accueil)
(1, 2, 'Commencer l aventure', 0, 0, 0, NULL),

-- Depuis page 2 (Rencontre Mark)
(2, 3, 'Ouais, je joue !', 0, 0, 0, NULL),
(2, 4, 'Je préfère m entraîner seul d abord.', 0, 0, 0, NULL),

-- Depuis page 3 (Entraînement Raimon) → choix objet
(3, 5, 'Prendre le bandeau de Mark Evans', 0, 0, 0, NULL),
(3, 6, 'Prendre les crampons d Axel Blaze', 0, 0, 0, NULL),

-- Depuis page 4 (Entraînement solo) → choix objet
(4, 7, 'Prendre le carnet de Jude Sharp', 0, 0, 0, NULL),
(4, 8, 'Rentrer sans rien toucher', 0, 0, 0, NULL),

-- Depuis pages 4A/4B/4C/4D → Demi-finale
(5, 9, 'Aller dormir, demain c est la demi-finale', 0, 0, 0, NULL),
(6, 9, 'Aller dormir, demain c est la demi-finale', 0, 0, 0, NULL),
(7, 9, 'Aller dormir, demain c est la demi-finale', 0, 0, 0, NULL),
(8, 9, 'Aller dormir, demain c est la demi-finale', 0, 0, 0, NULL),

-- Depuis page 9 (Demi-finale)
(9, 10, 'Tenter la Tornade de Feu dès la reprise', 0, 0, 0, NULL),
(9, 12, 'Jouer collectif, faire confiance à l équipe', 0, 0, 0, NULL),
(9, 13, 'Se sacrifier en défense pour libérer Jude Sharp', 0, 0, 0, NULL),

-- Depuis page 10 (Tornade succès) / page 11 (Tornade rebond) / 12 / 13 → Finale
(10, 14, 'Aller en finale !', 0, 0, 0, NULL),
(11, 14, 'Aller en finale !', 0, 0, 0, NULL),
(12, 14, 'Aller en finale !', 0, 0, 0, NULL),
(13, 14, 'Aller en finale !', 0, 0, 0, NULL),

-- Depuis page 14 (Finale) → fins
-- Fin victoire (courage OU technique >= 2)
(14, 15, 'Donner tout ce qu il te reste !', 2, 0, 0, NULL),
(14, 15, 'Donner tout ce qu il te reste !', 0, 2, 0, NULL),
-- Fin secrète (bandeau + courage 3)
(14, 16, 'Laisser parler ton instinct...', 3, 0, 0, 1),
-- Défense → victoire si stamina >= 2
(14, 15, 'Défendre le score et aller aux tirs au but', 0, 0, 2, NULL),
-- Abandon → défaite
(14, 17, 'Tu n en peux plus, tu demandes ta sortie', 0, 0, 0, NULL);

-- ============================================================
-- EXEMPLE : Création d une partie (pour les tests)
-- ============================================================
-- INSERT INTO partie (pseudo, page_actuelle) VALUES ('TestJoueur', 1);
-- INSERT INTO stats_joueur (id_partie, courage, technique, stamina) VALUES (1, 1, 1, 2);
