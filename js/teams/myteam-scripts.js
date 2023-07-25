//////////////////////////
// Chiamate al database //
//////////////////////////

/**
 * Effettua una richiesta AJAX al server per ottenere un elenco di squadre.
 * La risposta dal server viene interpretata come JSON.
 * 
 * @function fetchTeams
 * @throws {Error} Se la richiesta AJAX fallisce.
 */
function fetchTeams() {
  jQuery.ajax({
    url: 'http://localhost/profile/myteam-helper.php?action=get-teams',
    type: 'GET',
    dataType: 'json',
    success: function (response) {

    },
    error: function (xhr, status, error) {
      console.log(xhr.responseText);
    }
  });
}
/**
 * Effettua una richiesta AJAX al server per ottenere i dettagli di una squadra specifica identificata tramite teamId.
 * La risposta dal server viene interpretata come JSON.
 * 
 * @function fetchTeam
 * @param {number} teamId - L'identificativo unico della squadra di cui si vogliono ottenere i dettagli.
 * @throws {Error} Se la richiesta AJAX fallisce.
 */
function fetchTeam(teamId) {

  jQuery.ajax({
    url: 'http://localhost/profile/myteam-helper.php?action=get-team',
    type: 'POST',
    data: { id: teamId },
    dataType: 'json',
    success: function (response) {
      if (response.status == 'success') {
        $.magnificPopup.close()
      }
    },
    error: function (xhr, status, error) {
      console.log(xhr.responseText);
    }
  });
}

/**
 * Ottiene i dettagli di una società in base al nome del boss.
 * Effettua una richiesta AJAX al server per ottenere i dettagli della società.
 * La risposta dal server viene interpretata come JSON.
 * 
 * @function getSocietyByBoss
 * @param {string} boss - Il nome del boss della società di cui si vogliono ottenere i dettagli.
 * @returns {Promise} Una promessa che si risolverà con l'oggetto JSON contenente i dettagli della società
 *                    o verrà rifiutata con un errore in caso di problemi durante la richiesta AJAX.
 * @throws {Error} Se la richiesta AJAX fallisce.
 */
function getSocietyByBoss(boss) {
  return new Promise(function (resolve, reject) {
    $.ajax({
      url: 'http://localhost/profile/myteam-helper.php',
      method: 'GET',
      data: {
        action: 'get-society-by-boss',
        boss: boss
      },
      dataType: "json",
      success: function (response) {
        updateMyStaffpage(response.society);
        resolve(response.society);
      },
      error: function (xhr, status, error) {
        reject(error);
      }
    });
  });
}

/**
 * Ottiene i dettagli di una squadra in base al nome dell'allenatore.
 * Effettua una richiesta AJAX al server per ottenere i dettagli della squadra.
 * La risposta dal server viene interpretata come JSON.
 * 
 * @function getTeambyCoach
 * @param {string} coach - Il nome dell'allenatore della squadra di cui si vogliono ottenere i dettagli.
 * @returns {Promise} Una promessa che si risolverà con l'identificativo unico della squadra (team ID) ricevuto dalla risposta,
 *                    o verrà rifiutata con un errore in caso di problemi durante la richiesta AJAX.
 * @throws {Error} Se la richiesta AJAX fallisce.
 */
function getTeambyCoach(coach) {
  return new Promise(function (resolve, reject) {
    $.ajax({
      url: 'http://localhost/profile/myteam-helper.php?action=get-team-by-coach',
      method: 'GET',
      data: {
        coach: coach
      },
      dataType: "json",
      success: function (response) {
        updateMyTeampage(response.team);
        resolve(response.team.id);
      },
      error: function (xhr, status, error) {
        console.log(error);
        reject(error);
      }
    });
  });
}

/**
 * Ottiene i giocatori di una squadra in base al nome della squadra.
 * Effettua una richiesta AJAX al server per ottenere i dettagli dei giocatori della squadra.
 * La risposta dal server viene interpretata come JSON.
 * 
 * @function getPlayersbyTeam
 * @param {string} team - Il nome della squadra di cui si vogliono ottenere i giocatori.
 * @throws {Error} Se la richiesta AJAX fallisce.
 */
function getPlayersbyTeam(team) {
  $.ajax({
    url: 'http://localhost/profile/myteam-helper.php?action=get-players-by-team',
    method: 'GET',
    data: {
      team: team
    },
    dataType: "json",
    success: function (response) {
      updateCardVisibility(response.players);
    },
    error: function (xhr, status, error) {
      console.log(error);
    }
  });
}

/**
 * Ottiene gli allenatori in base all'email del capo.
 * Effettua una richiesta AJAX al server per ottenere i dettagli degli allenatori associati all'email del capo.
 * La risposta dal server viene interpretata come JSON.
 * 
 * @function getCoachesByBoss
 * @param {string} mail - L'email del capo per cui si vogliono ottenere gli allenatori associati.
 * @throws {Error} Se la richiesta AJAX fallisce.
 */
function getCoachesByBoss(mail) {
  $.ajax({
    url: 'http://localhost/profile/myteam-helper.php?action=get-coaches-by-boss',
    method: 'GET',
    data: {
      boss_email: mail
    },
    dataType: "json",
    success: function (response) {
      updateCardVisibility(response.coaches);
    },
    error: function (xhr, status, error) {
      console.log(error);
    }
  });
}

/**
 * Elimina il giocatore in base al bottone cliccato.
 * Effettua una richiesta AJAX al server per eliminare i dettagli del giocatore associato al bottone.
 * La risposta dal server viene interpretata come JSON.
 * 
 * @function deletePlayer
 * @param {Element} buttonElement - Il bottone cliccato che genera la richiesta.
 * @throws {Error} Se la richiesta AJAX fallisce.
 */
function deletePlayer(buttonElement) {
  // risalgo alla email del giocatore dal titolo della card
  var email = buttonElement.parentNode.querySelector('.card-title').innerText;
  $.ajax({
    url: 'http://localhost/profile/myteam-helper.php?action=delete-player',
    method: 'POST',
    data: {
      email: email
    },
    dataType: "json",
    success: function (response) {
      updateCardVisibility(response.players);
    },
    error: function (xhr, status, error) {
      console.log(error);
      console.log(xhr.responseText);
    }
  });
}

/**
 * Elimina l'allenatore in base al bottone cliccato.
 * Effettua una richiesta AJAX al server per eliminare i dettagli dell'allenatore associato al bottone.
 * La risposta dal server viene interpretata come JSON.
 * 
 * @function deletePlayer
 * @param {Element} buttonElement - Il bottone cliccato che genera la richiesta.
 * @param {String} managerMail - La mail del responsabile della società
 * @throws {Error} Se la richiesta AJAX fallisce.
 */
function deleteStaff(buttonElement, managerMail) {
  // risalgo alla email del giocatore dal titolo della card
  var email = buttonElement.parentNode.querySelector('.card-title').innerText;
  $.ajax({
    url: 'http://localhost/profile/myteam-helper.php?action=delete-staff',
    method: 'POST',
    data: {
      email: email,
      boss_email: managerMail
    },
    dataType: "json",
    success: function (response) {
      updateCardVisibility(response.coaches);
    },
    error: function (xhr, status, error) {
      console.log(error);
      console.log(xhr.responseText);
    }
  });
}

/**
 * Aggiorna la pagina della squadra con i dettagli della squadra ricevuti come parametro.
 * 
 * @function updateMyTeampage
 * @param {object} team - L'oggetto JSON contenente i dettagli della squadra da aggiornare sulla pagina.
 * @throws {Error} Se l'oggetto team non è valido o se gli elementi HTML specificati non sono presenti sulla pagina.
 */
function updateMyTeampage(team) {
  // Estrai le proprietà 'nome' e 'codice' dall'oggetto 'team' utilizzando il destructuring
  const { nome, codice } = team;

  // Trova l'elemento con l'ID "team-name" nella pagina
  const teamNameElement = document.getElementById("team-name");
  // Se l'elemento esiste
  if (teamNameElement) {
    // Imposta il contenuto di testo dell'elemento con il valore della proprietà 'nome'
    teamNameElement.textContent = nome;
  }

  // Trova l'elemento con l'ID "team-code" nella pagina
  const teamCodeElement = document.getElementById("team-code");
  // Se l'elemento esiste
  if (teamCodeElement) {
    // Imposta il testo dell'elemento con il valore della proprietà 'codice'
    teamCodeElement.innerText = codice;
  }

  // Trova il primo elemento input con attributo 'name' uguale a 'hidden-title-name'
  const hiddenTeamInput = document.querySelector("input[name='hidden-title-name']");
  // Se l'elemento esiste
  if (hiddenTeamInput) {
    // Imposta il valore dell'elemento input con il valore della proprietà 'nome'
    hiddenTeamInput.value = nome;
  }

  // Trova il primo elemento input con attributo 'name' uguale a 'hidden-code'
  const hiddenCodeInput = document.querySelector("input[name='hidden-code']");
  // Se l'elemento esiste
  if (hiddenCodeInput) {
    // Imposta il valore dell'elemento input con il valore della proprietà 'codice'
    hiddenCodeInput.value = codice;
  }
}

/**
 * Aggiorna le informazioni relative a una società all'interno della pagina web.
 * 
 * @param {Object[]} society - Un array contenente le informazioni sulla società.
 *                             Ogni elemento deve avere le proprietà 'nome' e 'codice',
 *                             rappresentanti rispettivamente il nome e il codice della società.
 * @returns {void} - La funzione non restituisce alcun valore.
 */
function updateMyStaffpage(society) {
  // Verifica se l'array 'society' ha almeno un elemento
  if (society.length > 0) {
    // Destructuring per estrarre 'nome' e 'codice' dal primo elemento dell'array 'society'
    const { nome, codice } = society[0];

    // Trova l'elemento con l'ID "society-name" e imposta il contenuto di testo con 'nome'
    const societyNameElement = document.getElementById("society-name");
    if (societyNameElement) {
      societyNameElement.textContent = nome;
    }

    // Trova l'elemento con l'ID "society-code" e imposta il contenuto di testo con 'codice'
    const societyCodeElement = document.getElementById("society-code");
    if (societyCodeElement) {
      societyCodeElement.textContent = codice;
    }

    // Trova il primo elemento input con attributo 'name' uguale a 'hidden-title-name' e imposta il valore con 'nome'
    const hiddenTeamInput = document.querySelector("input[name='hidden-title-name']");
    if (hiddenTeamInput) {
      hiddenTeamInput.value = nome;
    }

    // Trova il primo elemento input con attributo 'name' uguale a 'hidden-code' e imposta il valore con 'codice'
    const hiddenCodeInput = document.querySelector("input[name='hidden-code']");
    if (hiddenCodeInput) {
      hiddenCodeInput.value = codice;
    }
  }
}

/**
 * Aggiorna la visibilità delle card dei giocatori all'interno della pagina web.
 * 
 * @param {Object[]} players - Un array contenente le informazioni sui giocatori.
 *                             Ogni elemento deve avere almeno la proprietà 'email'
 *                             e può avere la proprietà 'locazione_immagine_profilo'.
 * @returns {void} - La funzione non restituisce alcun valore.
 */
function updateCardVisibility(players) {
  let n_players = players ? players.length : 0;

  const conditions = new Array(n_players).fill(true);

  const cards = document.querySelectorAll('.col-md-4');

  // Controlla se almeno una card è visibile
  let isAnyCardVisible = false;

  for (let i = 0; i < cards.length; i++) {
    if (conditions[i]) {
      isAnyCardVisible = true;
      const cardTitle = cards[i].querySelector('.card-title');
      const email = players[i]?.email || '';
      const cardImage = cards[i].querySelector('.card-img-top');

      cardTitle.textContent = email;
      cards[i].style.display = "block";

      if (players[i]?.locazione_immagine_profilo) {
        cardImage.src = players[i].locazione_immagine_profilo;
      }
    } else {
      cards[i].style.display = "none";
    }
  }
  // Se almeno una card è visibile, mostra la .row impostando lo stile a "block"
  // Altrimenti, nascondi la .row impostando lo stile a "none"
  const rowElement = document.querySelector('.row');
  rowElement.style.display = isAnyCardVisible ? 'block' : 'none';
}


