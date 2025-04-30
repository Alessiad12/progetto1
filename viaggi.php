<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crea Viaggio</title>
  <style>
    body {
  background-image: url('https://i.pinimg.com/736x/08/1f/24/081f245b11dbcc5f37cc116c5bb5b339.jpg'); /* Inserisci il percorso dell'immagine */
  background-size: cover; /* L'immagine copre l'intera area */
  background-position: center; /* Centra l'immagine */
  background-repeat: no-repeat; /* Evita che l'immagine si ripeta */
  background-color: #EAE1C1; /* Colore di sfondo di fallback */
  color: #1D3B5B;
  font-family: 'Playfair Display', serif;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  min-height: 100vh;
  margin: 20px;
  padding-top: 40px;
}

    .form-container {
      background-color: rgba(255, 255, 255, 0.66);
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 500px;
    }

    h1 {
      text-align: center;
      color: #333;
      font-size: 24px;
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }

    input[type="text"],
    input[type="date"],
    select,
    textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-sizing: border-box;
    }

    textarea {
      resize: vertical;
      height: 100px;
    }

    input[type="submit"] {
      background-color:rgb(76, 142, 175);
      color: white;
      border: none;
      padding: 15px 20px;
      width: 100%;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
      background-color:rgb(69, 113, 160);
    }

    .optional {
      font-size: 0.9em;
      color: #777;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h1>Crea il tuo viaggio</h1>
    <form action="salva_viaggio.php" method="POST">
      <label for="destinazione">Destinazione:</label>
      <input type="text" id="destinazione" name="destinazione" required>

      <label for="data_partenza">Data partenza:</label>
      <input type="date" id="data_partenza" name="data_partenza" required>

      <label for="data_ritorno">Data ritorno:</label>
      <input type="date" id="data_ritorno" name="data_ritorno" required>

      <label for="budget">Budget:</label>
      <select id="budget" name="budget">
        <option value="basso">Basso</option>
        <option value="medio">Medio</option>
        <option value="alto">Alto</option>
      </select>

      <label for="tipo_viaggio">Tipo di viaggio:</label>
      <select id="tipo_viaggio" name="tipo_viaggio">
        <option value="relax">🏖️ Rilassarsi in spiaggia</option>
        <option value="avventura">🏛️ Imparare nei musei</option>
        <option value="cultura">🍴 Provare nuovi ristoranti</option>
        <option value="party">🧗‍♂️ Avventure all'aria aperta</option>
      </select>

      <label for="lingua">Lingua:</label>
      <input type="text" id="lingua" name="lingua" required>

      <label for="compagnia">Compagnia ideale:</label>
      <input type="text" id="compagnia" name="compagnia" placeholder="es. gruppo, coppia, solo donne">

      <label for="descrizione">Descrizione:</label>
      <textarea id="descrizione" name="descrizione"></textarea>

      <input type="submit" value="Crea viaggio">
    </form>
  </div>
</body>
</html>
