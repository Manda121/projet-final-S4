CREATE DATABASE tp_flight CHARACTER SET utf8mb4;

USE tp_flight;

CREATE TABLE etudiant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(100),
    age INT
);
insert into etudiant(nom,prenom,email,age) values('Razafy','Koto','kotogmail.com','20'),
                                                 ('Rabary','Tovo','tovogmail.com','21'),
                                                 ('Rasoa','Nivo','Nivogmail.com','20');










                                                 
                                                    