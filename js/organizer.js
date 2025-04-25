// js/organizer.js
document.addEventListener('DOMContentLoaded', async () => {
    const params = new URLSearchParams(window.location.search);
    const planId = params.get('planId');
    if (!planId) {
      document.getElementById('plan-container').innerHTML = '<p>Plan ID mancante.</p>';
      return;
    }
  
    try {
      const res = await fetch(`organizer.php?planId=${planId}`);
      if (!res.ok) throw new Error('Server ' + res.status);
      const data = await res.json();
      if (data.error) throw new Error(data.error);
  
      renderPlan(data);
    } catch (err) {
      console.error(err);
      document.getElementById('plan-container').innerHTML =
        `<p>Errore nel caricamento: ${err.message}</p>`;
    }
  });
  
  function renderPlan({ plan, tappe }) {
    const container = document.getElementById('plan-container');
    container.innerHTML = `
      <section>
        <h2>Viaggio #${plan.id}</h2>
        <p>Creato il: ${new Date(plan.created_at).toLocaleDateString()}</p>
      </section>
      <section>
        <h3>Tappe</h3>
        <ul>
          ${tappe.map(t => `<li>${t.nome} (${t.data_inizio} → ${t.data_fine})</li>`).join('')}
        </ul>
      </section>
      <button id="add-tappa">Aggiungi nuova tappa</button>
    `;
  
    document.getElementById('add-tappa').addEventListener('click', () => {
      // qui potresti aprire un form o un modal per aggiungere tappe
      alert('Qui apri il form per aggiungere una tappa.');
    });
  }
  