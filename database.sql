CREATE TABLE utenti (
  id SERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  nickname TEXT UNIQUE NOT NULL,
  email TEXT UNIQUE NOT NULL,
  data_di_nascita DATE,
  password TEXT NOT NULL
);

CREATE TABLE profili (
  id SERIAL PRIMARY KEY,
  email TEXT UNIQUE NOT NULL,
  nome VARCHAR(100),
  eta INT,
  bio TEXT,
  colore_sfondo VARCHAR(10) default '#faf3bfc4',
  data_di_nascita DATE,
  immagine_profilo TEXT,
  posizione_immagine TEXT,
  FOREIGN KEY (id) REFERENCES utenti(id)
);

--'salvare viaggio:' 
CREATE TABLE viaggi (
  id SERIAL PRIMARY KEY,
  user_id INT,
  destinazione VARCHAR(100),
  data_partenza DATE,
  data_ritorno DATE,
  budget VARCHAR(20),
  tipo_viaggio VARCHAR(50),
  lingua VARCHAR(50),
  compagnia VARCHAR(50),
  descrizione TEXT,
  FOREIGN KEY (user_id) REFERENCES utenti(id)
);

CREATE TABLE viaggi_utenti (
  viaggio_id INT,
  user_id INT,
  ruolo VARCHAR(20) DEFAULT 'partecipante', -- o 'ideatore'
  PRIMARY KEY (viaggio_id, user_id),
  FOREIGN KEY (viaggio_id) REFERENCES viaggi(id),
  FOREIGN KEY (user_id) REFERENCES utenti(id)
);