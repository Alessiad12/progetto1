  SELECT v.*,p.*, viaggi.*
  FROM viaggi_terminati v join profili p on 
  p.id=v.utente_id join viaggi on viaggi.id=v.viaggio_id
  WHERE viaggio_id=3;

  select * from profili;
  select * from viaggi_terminati;

   SELECT p.nome,p.immagine_profilo, v.descrizione
  FROM viaggi_terminati v
  JOIN profili p ON p.id = v.utente_id
  WHERE v.viaggio_id = 3;

 SELECT AVG(valutazione) AS media_valutazione 
 FROM viaggi_terminati 
 WHERE viaggio_id= 3;