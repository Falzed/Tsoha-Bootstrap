INSERT INTO Kayttaja (nimi, email, password) VALUES ('Matti', 'matti@gmail.com', 'passwd');
INSERT INTO Kayttaja (nimi, email, password) VALUES ('Tuomo', 'tuomo@gmail.com', 'salis');
INSERT INTO Kayttaja (nimi, email, password) VALUES ('admin', 'admin@fakemail.com', 'admin');
INSERT INTO Askare (nimi, kayttaja_id, description, prioriteetti, luokat, added) VALUES ('X', '1', 'blah blah blah', 5, '1', NOW());
INSERT INTO Luokka (nimi) VALUES ('1');
INSERT INTO AskareittenLuokat (askare_id, luokka_id) VALUES (1, 1);