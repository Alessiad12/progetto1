<form action="salva_viaggio.php" method="POST">
  <label>Destinazione:</label>
  <input type="text" name="destinazione" required><br>

  <label>Data partenza:</label>
  <input type="date" name="data_partenza" required><br>

  <label>Data ritorno:</label>
  <input type="date" name="data_ritorno" required><br>

  <label>Budget:</label>
  <select name="budget">
    <option value="basso">Basso</option>
    <option value="medio">Medio</option>
    <option value="alto">Alto</option>
  </select><br>

  <label>Tipo di viaggio:</label>
  <select name="tipo_viaggio">
    <option value="relax">Relax</option>
    <option value="avventura">Avventura</option>
    <option value="cultura">Cultura</option>
    <option value="party">Party</option>
  </select><br>

  <label>Lingua:</label>
  <input type="text" name="lingua" required><br>

  <label>Compagnia ideale:</label>
  <input type="text" name="compagnia" placeholder="es. gruppo, coppia, solo donne"><br>

  <label>Descrizione:</label><br>
  <textarea name="descrizione"></textarea><br>

  <input type="submit" value="Crea viaggio">
</form>
