CREATE TABLE Kayttaja(
    id SERIAL PRIMARY KEY,
    nimi varchar(50) NOT NULL,
    email varchar(50) NOT NULL,
    password varchar(50) NOT NULL
);

CREATE TABLE Askare(
    id SERIAL PRIMARY KEY,
    kayttaja_id INTEGER REFERENCES Kayttaja(id),
    nimi varchar(50) NOT NULL,
    description varchar(500),
    prioriteetti INTEGER,
    added DATE
);

CREATE TABLE Luokka(
    id SERIAL PRIMARY KEY,
    nimi UNIQUE varchar(50)    
);

CREATE TABLE AskareittenLuokat(
    askare_id INTEGER REFERENCES Askare(id),
    luokka_id INTEGER REFERENCES Luokka(id),  
    luokka_nimi varchar(50) REFERENCES Luokka(nimi)
);