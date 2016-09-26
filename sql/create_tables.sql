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