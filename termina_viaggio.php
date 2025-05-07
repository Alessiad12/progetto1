<?php
session_start();
if (!isset($_SESSION['id_utente'])) {
  header('Location: login.php');
  exit;
}

$utente_id = $_SESSION['id_utente'];
$viaggio_id = $_GET['viaggio_id'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registra la tua esperienza – Wanderlust</title>
  <style>
    /* Palette */
    :root {
      --cream: #FDF7E3;
      --navy: #0A2342;
      --navy-light: #12315C;
    }
    /* Reset base */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: var(--cream);
      color: var(--navy);
      font-family: sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 2rem;
    }
    /* Card a sinistra, più grande */
    .card {
      background: #FFF;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      padding: 2rem;
      width: 600px;
    }
    .card h1 {
      font-size: 1.8rem;
      margin-bottom: 1.5rem;
      color: var(--navy);
      text-align: left;
    }
    form > div { margin-bottom: 1.5rem; }
    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
    }
    textarea {
      width: 100%;
      min-height: 120px;
      border: 1px solid rgba(10,35,66,0.3);
      border-radius: 6px;
      padding: 0.75rem;
      resize: vertical;
      font-size: 1rem;
    }
    /* Star rating */
    .stars {
      display: flex;
      direction: rtl;
      gap: 0.25rem;
    }
    .stars input { display: none; }
    .stars label {
      font-size: 2rem;
      color: rgba(0,0,0,0.2);
      cursor: pointer;
      transition: color 0.2s;
    }
    .stars label:hover,
    .stars label:hover ~ label,
    .stars input:checked ~ label {
      color: gold;
    }
    /* Photo slots */
    .photo-slots {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 0.5rem;
    }
    .photo-slot {
      position: relative;
      width: 100%;
      padding-top: 100%; /* square */
      border: 2px dashed rgba(10,35,66,0.3);
      border-radius: 8px;
      overflow: hidden;
    }
    .photo-slot input {
      position: absolute;
      width: 0;
      height: 0;
      overflow: hidden;
    }
    .photo-slot label {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      font-size: 2rem;
      color: rgba(10,35,66,0.5);
      cursor: pointer;
    }
    /* Submit button */
    .btn-submit {
      background: var(--navy);
      color: #fff;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn-submit:hover {
      background: var(--navy-light);
    }
  </style>
</head>
<body>
  <div class="card">
    <h1> Il tuo viaggio</h1>
    <form action="salva_viaggio_terminato.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="utente_id" value="<?= htmlspecialchars($utente_id) ?>">
      <input type="hidden" name="viaggio_id" value="<?= htmlspecialchars($viaggio_id) ?>">

      <div>
        <label for="descrizione">Descrizione</label>
        <textarea id="descrizione" name="descrizione" required></textarea>
      </div>

      <div>
        <label>Valutazione</label>
        <div class="stars">
          <input type="radio" name="valutazione" id="star5" value="5"><label for="star5">★</label>
          <input type="radio" name="valutazione" id="star4" value="4"><label for="star4">★</label>
          <input type="radio" name="valutazione" id="star3" value="3"><label for="star3">★</label>
          <input type="radio" name="valutazione" id="star2" value="2"><label for="star2">★</label>
          <input type="radio" name="valutazione" id="star1" value="1"><label for="star1">★</label>
        </div>
      </div>

      <div>
        <label>Foto (5 slot)</label>
        <div class="photo-slots">
          <?php for ($i = 0; $i < 5; $i++): ?>
            <div class="photo-slot">
              <input type="file" name="foto[]" accept="image/*" id="foto<?= $i ?>">
              <label for="foto<?= $i ?>">+</label>
            </div>
          <?php endfor; ?>
        </div>
      </div>

      <button type="submit" class="btn-submit">Salva Esperienza</button>
    </form>
  </div>
</body>
</html>
