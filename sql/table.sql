create DATABASE finance;

use finance;

create table etablissement(
    id_etablissement int AUTO_INCREMENT PRIMARY key,
    nom VARCHAR(100)
);

create table fond(
    id_fond int AUTO_INCREMENT PRIMARY key,
    id_etablissement int,
    montant numeric,
    foreign key (id_etablissement) references etablissement(id_etablissement)
);

create table user(
    id_user int AUTO_INCREMENT PRIMARY key,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(100),
    date_de_naissance date,
    mot_de_passe VARCHAR(100),
    role_user ENUM('finance', 'client')
);

create table type_pret(
    id_type_pret int AUTO_INCREMENT PRIMARY key,
    libelle VARCHAR(100),
    id_etablissement int,
    taux numeric,
    foreign key (id_etablissement) references etablissement(id_etablissement)
);

create table pret(
    id_pret int AUTO_INCREMENT PRIMARY key,
    id_type_pret int,
    date_pret date,
    description text,
    montant numeric,
    date_limite date,
    foreign key (id_type_pret) references type_pret(id_type_pret)
);

create table remise(
    id_remise int AUTO_INCREMENT PRIMARY key,
    id_pret int,
    montant numeric,
    date_remise date,
    foreign key (id_pret) references pret(id_pret)
);