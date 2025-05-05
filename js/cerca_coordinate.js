
  async function ottieniCoordinate(destinazione) {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(destinazione)}`;
    const response = await fetch(url);
    const dati = await response.json();
    if (dati.length > 0) {
      return {
        lat: parseFloat(dati[0].lat),
        lon: parseFloat(dati[0].lon)
      };
    } else {
      throw new Error("Destinazione non trovata.");
    }
  }
  
  document.getElementById("form-viaggio").addEventListener("submit", async function (e) {
    e.preventDefault();
    const form = this;
    const destinazione = form.destinazione.value;
  
    try {
      const coord = await ottieniCoordinate(destinazione);
  
      // Aggiungi campi nascosti al form
      const latInput = document.createElement("input");
      latInput.type = "hidden";
      latInput.name = "latitudine";
      latInput.value = coord.lat;
      form.appendChild(latInput);
  
      const lonInput = document.createElement("input");
      lonInput.type = "hidden";
      lonInput.name = "longitudine";
      lonInput.value = coord.lon;
      form.appendChild(lonInput);
  
      // Ora puoi inviare il form con anche le coordinate
      form.submit();
    } catch (err) {
      alert("Errore: " + err.message);
    }
  });

  