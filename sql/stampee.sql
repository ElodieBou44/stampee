DROP TABLE IF EXISTS utilisateur;

--

-- Structure de la table utilisateur

--

CREATE TABLE
    utilisateur (
        utilisateur_id int UNSIGNED NOT NULL AUTO_INCREMENT,
        utilisateur_nom varchar(255) NOT NULL,
        utilisateur_prenom varchar(255) NOT NULL,
        utilisateur_courriel varchar(255) NOT NULL UNIQUE,
        utilisateur_mdp varchar(255) NOT NULL,
        utilisateur_renouveler_mdp char(3) NOT NULL DEFAULT 'oui',
        utilisateur_profil varchar(255) NOT NULL,
        PRIMARY KEY (utilisateur_id)
    ) ENGINE = InnoDB DEFAULT CHARSET = UTF8;

INSERT INTO utilisateur
VALUES (
        1,
        "Dac",
        "Pierre",
        "pierre.dac@site1.ca",
        SHA2("a1b2c3d4e5", 512),
        "oui",
        "administrateur"
    ), (
        2,
        "Tremblay",
        "Jean",
        "jean.tremblay@site2.ca",
        SHA2("f1g2h3i4j5", 512),
        "oui",
        "editeur"
    ), (
        3,
        "Lepetit",
        "Olivier",
        "olivier.lepetit@site3.ca",
        SHA2("k1l2m3n4o5", 512),
        "oui",
        "correcteur"
    ), (
        4,
        "Legrand",
        "Jacques",
        "jacques.legrand@site4.ca",
        SHA2("p1q2r3s4t5", 512),
        "oui",
        "client"
    );

INSERT INTO timbre
VALUES (
        12,
        "Thailand #58 Used fine to very fine single Cat$122 1870, ½d beige",
        "1870-06-07",
        "Thaïlande",
        "Bonne",
        "2cm x 2cm",
        "oui",
        19,
        "brun",
        1,
        1
    ), (
        13,
        "Japan 1990 Machin 17p CB 'HC' perfin cyl 17 corner blk of 6 um, ditto.",
        "1950-05-10",
        "Japon",
        "Moyenne",
        "3.5cm x 4cm",
        "oui",
        5,
        "rouge",
        2,
        1
    ), (
        3,
        "Australia 1920 2d blue/white Reg Envelope with sender address HG5",
        "1920-01-17",
        "Australie",
        "Excellente",
        "2.5cm x 3cm",
        "non",
        3,
        "vert",
        3,
        2
    ), (
        4,
        "Stamps - Italy - Scott# 292, 300 - Used & Mint Hinged Part Set of 2.",
        "2000-10-20",
        "Italie",
        "Bonne",
        "4cm x 5cm",
        "oui",
        25,
        "bleu",
        4,
        2
    ), (
        5,
        "USA 1943 Manial Itdlmsa Cover to Manila PI Overprint.",
        "1943-03-21",
        "États-Unis",
        "Endommagé",
        "2.3cm x 2.3cm",
        "non",
        32,
        "blanc",
        5,
        3
    ), (
        6,
        "US #270 SCV $105.00 VF mint never hinged, super fresh color, well centered for this issue, Nice! SCV",
        "1921-04-16",
        "États-Unis",
        "Excellente",
        "3cm x 3.5cm",
        "oui",
        45,
        "orange",
        6,
        3
    ), (
        7,
        "Scott 1141 1960 4c Thomas Jefferson Quote Issue Mint XF NH",
        "1960-06-22",
        "États-Unis",
        "Moyenne",
        "4.5cm x 2.5cm",
        "non",
        6,
        "bleu",
        7,
        8
    ); -- , (
    --     8,
    --     "U.S. #143 USED",
    --     "1986-02-14",
    --     "États-Unis",
    --     "Parfaite",
    --     "2.7cm x 3.2cm",
    --     "oui",
    --     33,
    --     "vert",
    --     8,
    --     8
    -- ), (
    --     9,
    --     "1ch Salatin Service Issue MH",
    --     "2000-05-01",
    --     "Inde",
    --     "Endommagé",
    --     "3.8cm x 4.2cm",
    --     "oui",
    --     5,
    --     "orange",
    --     9,
    --     9
    -- ), (
    --     10,
    --     "Germany #36 VF Unused CV $75.00 (X5080)",
    --     "1930-11-06",
    --     "Allemagne",
    --     "Bonne",
    --     "2.2cm x 2.8cm",
    --     "non",
    --     18,
    --     "rouge",
    --     10,
    --     9
    -- ), (
    --     11,
    --     "US #270 SCV $105.00 VF mint never hinged, super fresh color, well centered for this issue, Nice! SCV",
    --     "1945-08-14",
    --     "États-Unis",
    --     "Excellente",
    --     "2cm x 2cm",
    --     "oui",
    --     18,
    --     "rouge",
    --     11,
    --     11
    -- )

INSERT INTO enchere
VALUES (
        1,
        '2023-06-30',
        55.00,
        19,
        1,
        '2023-07-30',
        122.00,
        "Mathilde Boulanger",
        13
    ), (
        2,
        '2023-07-12',
        10.00,
        0,
        1,
        '2023-09-12',
        null,
        null,
        14
    ), (
        3,
        '2023-07-05',
        13.00,
        5,
        0,
        '2023-09-05',
        null,
        null,
        2
    ), (
        4,
        '2023-06-02',
        28.00,
        7,
        1,
        '2023-07-02',
        14.00,
        'Patrice Gendreau',
        3
    ), (
        5,
        '2023-06-02',
        30.00,
        null,
        0,
        '2023-07-02',
        null,
        null,
        8
    ), (
        6,
        '2023-06-02',
        30.00,
        null,
        0,
        '2023-07-02',
        null,
        null,
        9
    ), (
        7,
        '2023-06-02',
        30.00,
        null,
        1,
        '2023-07-02',
        null,
        null,
        11
    );

INSERT INTO  `image` 
VALUES (
    1,
    'bgiuwed.jpg',
    1,
    'primaire'
),
(
    2,
    'jhtrge.png', 
    2,
    'primaire'
),
(
    3,
    'gersd.jpg',
    3,
    'primaire'
),
(
    4,
    'fwjoec.jpg',
    4,
    'primaire'
),
(
    5,
    'wjoxwq.png',
    5,
    'primaire'
),
(
    6,
    'cwkpwed.jpg',
    6,
    'primaire'
),
(
    7,
    'wepkcw.png',
    7,
    'primaire'
),
(
    8,
    'g4wedw.png',
    1,
    'secondaire'
),
(
    9,
    'wejow.png',
    2,
    'secondaire'
);


-- SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
--        GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images
-- FROM timbre t
-- LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
-- GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id;
SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
       GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
       e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id
FROM timbre t
LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id; 
