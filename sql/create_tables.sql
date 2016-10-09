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
    luokat varchar(500),
    added DATE
);

CREATE TABLE Luokka(
    id SERIAL PRIMARY KEY,
    nimi varchar(50)
);

CREATE TABLE LuokanAlaluokat(
    ylaluokan_id INTEGER REFERENCES Luokka(id),
    alaluokan_id INTEGER REFERENCES Luokka(id)
);

CREATE TABLE AskareittenLuokat(
    askare_id INTEGER REFERENCES Askare(id),
    luokka_id INTEGER REFERENCES Luokka(id),
    kayttaja_id INTEGER REFERENCES Kayttaja(id),
    luokka_nimi varchar(50)
);