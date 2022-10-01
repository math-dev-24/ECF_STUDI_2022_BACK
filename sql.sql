CREATE TABLE `user`(
                       `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                       `email` VARCHAR(100) NOT NULL,
                       `password` VARCHAR(200) NOT NULL,
                       `user_active` TINYINT(1) NOT NULL,
                       `user_name` VARCHAR(100) NOT NULL,
                       `first_connect` TINYINT(1) NOT NULL,
                       `is_admin` TINYINT(1) NOT NULL,
                       `profil_url` VARCHAR(255) NOT NULL
);

CREATE TABLE `historique`(
                             `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                             `text` VARCHAR(255) NOT NULL
);

CREATE TABLE `partner`(
                          `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                          `user_id` INT NOT NULL UNIQUE,
                          `partner_name` VARCHAR(100) NOT NULL,
                          `partner_active` TINYINT(1) NOT NULL,
                          `gestion_id` INT NOT NULL,
                          `logo_url` VARCHAR(255) NOT NULL,
                          FOREIGN KEY(user_id) REFERENCES user(id),
                          FOREIGN KEY(gestion_id) REFERENCES gestion(id)
);

CREATE TABLE `struct`(
                         `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                         `user_id` INT NOT NULL UNIQUE,
                         `partner_id` INT NOT NULL,
                         `struct_name` VARCHAR(100) NOT NULL,
                         `struct_active` TINYINT(1) NOT NULL,
                         `struct_address` VARCHAR(150) NOT NULL,
                         `strict_city` VARCHAR(50) NOT NULL,
                         `struct_postal` INT NOT NULL CHECK(struct_postal > 0),
                         `gestion_id` INT NOT NULL,
                         FOREIGN KEY(user_id) REFERENCES user(id),
                         FOREIGN KEY(partner_id) REFERENCES partner(id),
                         FOREIGN KEY(gestion_id) REFERENCES gestion(id)

);

CREATE TABLE `gestion`(
                          `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                          `v_vetement` TINYINT(1) NOT NULL,
                          `v_boisson` TINYINT(1) NOT NULL,
                          `c_particulier` TINYINT(1) NOT NULL,
                          `c_crosstrainning` TINYINT(1) NOT NULL,
                          `c_pilate` TINYINT(1) NOT NULL
)