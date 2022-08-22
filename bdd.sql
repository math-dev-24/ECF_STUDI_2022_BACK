
CREATE TABLE user
(
    user_id INT NOT NULL PRIMARY KEY ,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(150) NOT NULL,
    user_active TINYINT NOT NULL,
    user_name VARCHAR(75) NOT NULL,
    user_grade INT NOT NULL,
    user_description VARCHAR(250) NULL,
    logo_url VARCHAR(150) NULL,
    url VARCHAR(150) NULL
);

CREATE TABLE partner
(
    partner_id INT NOT NULL PRIMARY KEY ,
    user_id INT NOT NULL ,
    partner_name VARCHAR(100) NOT NULL ,
    partner_active TINYINT NOT NULL ,
    gestion_id INT NOT NULL
);

CREATE TABLE structure
(
    struct_id INT NOT NULL PRIMARY KEY ,
    user_id INT NOT NULL ,
    partner_id INT NOT NULL ,
    struct_name VARCHAR(75) NOT NULL ,
    struct_active TINYINT NOT NULL ,
    gestion_id INT NOT NULL
);

CREATE TABLE gestion
(
    gestion_id INT NOT NULL PRIMARY KEY ,
    vente_vetement TINYINT NOT NULL ,
    vente_produit TINYINT not null
)
