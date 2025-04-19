CREATE TABLE utenti (
  id SERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  nickname TEXT UNIQUE NOT NULL,
  email TEXT UNIQUE NOT NULL,
  vacanza VARCHAR(50),
  tipo_vacanza VARCHAR(50),
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

--'salvare viaggio:' 
CREATE TABLE viaggi (
  id SERIAL PRIMARY KEY,
  utente INT NOT NULL REFERENCES utenti(id),
  foto TEXT NOT NULL,
  vacanza VARCHAR(50) NOT NULL,
  scopo       VARCHAR(50) NOT NULL,
  creazione    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);