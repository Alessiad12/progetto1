CREATE TABLE utenti (
  id SERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  username TEXT UNIQUE NOT NULL,
  email TEXT UNIQUE NOT NULL,
  password TEXT NOT NULL
);

CREATE TABLE profili (
  id SERIAL PRIMARY KEY,
  id_utente INT,
  nome VARCHAR(100),
  eta INT,
  bio TEXT,
  colore_sfondo VARCHAR(10) default '#faf3bfc4',
  FOREIGN KEY (id_utente) REFERENCES utenti(id)
);