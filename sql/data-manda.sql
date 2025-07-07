-- Insertion d'établissements
INSERT INTO finance_s4_etablissement (nom) VALUES 
('Banque Nationale'),
('Crédit Municipal'),
('Caisse d\'Epargne');

-- Insertion de fonds pour les établissements
INSERT INTO finance_s4_fond (id_etablissement, montant) VALUES 
(1, 10000000),
(2, 5000000),
(3, 7500000);

-- Insertion d'utilisateurs
INSERT INTO finance_s4_user (nom, prenom, email, date_de_naissance, mot_de_passe, role_user) VALUES 
('Dupont', 'Jean', 'jean.dupont@email.com', '1985-05-15', 'password123', 'client'),
('Martin', 'Sophie', 'sophie.martin@email.com', '1990-08-22', 'securepass', 'client'),
('Bernard', 'Pierre', 'pierre.bernard@email.com', '1978-03-10', 'finance2023', 'finance'),
('Petit', 'Marie', 'marie.petit@email.com', '1982-11-30', 'mariepass', 'finance');

-- Association utilisateurs-établissements
INSERT INTO finance_s4_etablissement_user (id_user, id_etablissement) VALUES 
(1, 1),
(2, 2),
(3, 1),
(4, 3);

-- Insertion de types de prêts
INSERT INTO finance_s4_type_pret (libelle, id_etablissement, taux, montant_min, montant_max, delai_mois_max) VALUES 
('Prêt personnel', 1, 4.5, 1000, 50000, 60),
('Prêt immobilier', 1, 2.5, 50000, 500000, 240),
('Micro-crédit', 2, 1.5, 100, 5000, 24),
('Prêt étudiant', 3, 0.5, 500, 20000, 120);

-- Insertion de demandes de prêt
INSERT INTO finance_s4_pret (id_user, id_type_pret, date_pret, description, montant, date_limite, etat) VALUES 
(1, 1, '2023-01-10', 'Achat de voiture', 15000, '2028-01-10', 'validee'),
(1, 2, '2023-02-15', 'Achat appartement', 200000, '2043-02-15', 'en attente'),
(2, 3, '2023-03-05', 'Création micro-entreprise', 3000, '2025-03-05', 'validee'),
(2, 4, '2023-04-20', 'Frais de scolarité', 8000, '2033-04-20', 'refusee');

-- Insertion de remises
INSERT INTO finance_s4_remise (id_pret, montant, date_remise) VALUES 
(1, 500, '2023-02-01'),
(3, 200, '2023-04-01');