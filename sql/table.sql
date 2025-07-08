create DATABASE finance_s4;

use finance_s4;

create table finance_s4_etablissement(
    id_etablissement int AUTO_INCREMENT PRIMARY key,
    nom VARCHAR(100)
);

create table finance_s4_fond(
    id_fond int AUTO_INCREMENT PRIMARY key,
    id_etablissement int,
    montant numeric,
    foreign key (id_etablissement) references finance_s4_etablissement(id_etablissement)
);

create table finance_s4_user(
    id_user int AUTO_INCREMENT PRIMARY key,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(100),
    date_de_naissance date,
    mot_de_passe VARCHAR(100),
    role_user ENUM('finance', 'client') 
);

create table finance_s4_etablissement_user(
    id_etablissement_user int AUTO_INCREMENT PRIMARY key,
    id_user int,
    id_etablissement int,
    foreign key (id_user) references finance_s4_user(id_user),
    foreign key (id_etablissement) references finance_s4_etablissement(id_etablissement)
);

create table finance_s4_type_pret(
    id_type_pret int AUTO_INCREMENT PRIMARY key,
    libelle VARCHAR(100),
    id_etablissement int,
    montant_min numeric,
    montant_max numeric,
    delai_mois_max int default 1,
    foreign key (id_etablissement) references finance_s4_etablissement(id_etablissement)
);

create table finance_s4_taux(
    id_taux int AUTO_INCREMENT PRIMARY key,
    id_type_pret int,
    taux numeric,
    foreign key (id_type_pret) references finance_s4_type_pret(id_type_pret)
);

create table finance_s4_pret(
    id_pret int AUTO_INCREMENT PRIMARY key,
    id_user int,
    id_taux int,
    taux_assurance numeric,
    date_pret date,
    description text,
    montant numeric,
    date_limite date,
    etat ENUM("en attente", "validee", "refusee") default "en attente",
    foreign key (id_taux) references finance_s4_taux(id_taux),
    foreign key (id_user) references finance_s4_user(id_user)
);

create table finance_s4_remise(
    id_remise int AUTO_INCREMENT PRIMARY key,
    id_pret int,
    montant numeric,
    date_remise date,
    foreign key (id_pret) references finance_s4_pret(id_pret)
);


CREATE TABLE finance_s4_simulation (
    id_simulation INT AUTO_INCREMENT PRIMARY KEY,
    id_pret INT,
    montant NUMERIC DEFAULT 0,
    taux_annuel NUMERIC DEFAULT 0,
    date_pret DATE,
    date_limite DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pret) REFERENCES finance_s4_pret(id_pret)
);

CREATE TABLE finance_s4_simulation_echeancier (
    id_echeancier INT AUTO_INCREMENT PRIMARY KEY,
    id_simulation INT,
    mois INT,
    capital_restant NUMERIC DEFAULT 0,
    interet NUMERIC DEFAULT 0,
    capital_rembourse NUMERIC DEFAULT 0,
    annuite NUMERIC DEFAULT 0,
    date_paiement DATE,
    FOREIGN KEY (id_simulation) REFERENCES finance_s4_simulation(id_simulation)
);

ALTER TABLE finance_s4_fond MODIFY COLUMN montant numeric DEFAULT 0;

ALTER TABLE finance_s4_type_pret MODIFY COLUMN montant_min numeric DEFAULT 0;
ALTER TABLE finance_s4_type_pret MODIFY COLUMN montant_max numeric DEFAULT 0;

ALTER TABLE finance_s4_pret MODIFY COLUMN montant numeric DEFAULT 0;

ALTER TABLE finance_s4_remise MODIFY COLUMN montant numeric DEFAULT 0;

ALTER TABLE finance_s4_pret ADD delai_premier_remboursement INT NULL DEFAULT 0;
ALTER TABLE finance_s4_remise MODIFY COLUMN montant numeric DEFAULT 0;