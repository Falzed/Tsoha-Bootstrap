CREATE TABLE Kayttaja(
    id SERIAL PRIMARY KEY,
    name varchar(50) NOT NULL,
    password varchar(50) NOT NULL
);

CREATE TABLE Askare(
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES Kayttaja(id),
    name varchar(50) NOT NULL,
    description varchar(400),
    prioriteetti INTEGER,
    luokat varchar(500),
    added DATE
);