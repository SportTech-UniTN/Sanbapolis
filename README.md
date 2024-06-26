# Sanbapolis

Internship @ UniTN, business logic development of the management system of the sports facility located at Sanbartolameo

# Come utilizzarlo

## Prerequisiti

Per utilizzare il sistema bisogna avere apache, una database relazionale sql, composer, un server STMP ed almeno una IP-camera 

## Scarica

Si può clonare l'intera repository direttamente nella propria cartella apache, solitamente `\var\www\html` oppure `xampp\htdocs` se si utilizza un ambiente in locale tramite xampp.

``` bash
git clone https://github.com/SportTech-UniTN/Sanbapolis.git
```

## Imposta
Bisogna apportare alcune aggiunte e/o modifiche al sistema per far si che il vostro funzioni correttamente

### Installazioni sul server
Nonostante siano già presenti all'interno delle cartelle del progetto è importante installare sul proprio sistema composer e ffmpeg.

#### Composer
     ```
     sudo apt-get install composer
     ```

#### FFmpeg
   - **Ubuntu (apt)**:
     ```
     sudo apt-get install ffmpeg
     ```

   - **Fedora (dnf)**:
     ```
     sudo dnf install ffmpeg
     ```

   - **Homebrew (MacOS)**:
     ```
     brew install ffmpeg
     ```


   Dopo aver completato il processo di installazione, verifica che FFmpeg sia stato installato correttamente eseguendo il comando `ffmpeg -version` nel terminale.

``` bash
crontab -e
```
### Permessi
Per ragioni di sicurezza il webserver non può leggere o scrivere di default sulle cartelle del progetto.
Utilizza il comando chown per cambiare il proprietario della cartella in www-data (Apache web server). Ecco il comando da eseguire:

``` bash
sudo chown www-data:www-data /assets/profileImg/
sudo chown www-data:www-data /editing_video/storage_video/
```

Impostiamo adesso i permessi

``` bash
sudo chmod 755 /assets/profileImg/
sudo chmod 755 /editing_video/storage_video/
```

### Chronjob

#### Unix o MacOS

Potete impostare un _chronjob_ nel seguente modo:

1. aprire la tabella dei chronjob

``` bash
crontab -e
```

2. se è la prima volta che viene aperto, si deve scegliere un editor, potete scegliere quello che preferite senza vincoli
2. aggiungere il chronjob nel file appena aperto

``` bash
*/5 * * * * php /var/www/html/modals/ABAC.php
```

#### Windows

Per ottenere un chronjob su Windows bisogna impostare un task scheduler nel seguente modo:

1. Apri il "Task Scheduler" o "Utilità di pianificazione" cercandolo nel menu Start o nel Pannello di controllo.
2. Click su "Create Basic Task" o "Create Task" nella sezione "Actions" che si trova sulla destra.
3. Segui la procedura guidata per configurare il task, specificando il trigger di 5 minuti.

### Database

Popola il tuo database connettendoti al tuo mysql e lanciando gli script presenti nella cartella `db`.
Importante: Si prega di prestare attenzione al fatto che non tutte le informazioni nel database sono prepopolabili automaticamente. Per esempio, è fondamentale assicurarsi che nella tabella "telecamere" siano correttamente inseriti gli indirizzi IP delle tue telecamere. Affinché l'intero sistema funzioni in modo ottimale, ti consigliamo di rivedere attentamente ciascuna tabella e le relative relazioni.

### Config File
Nella cartella `modals` il file `config.php` contiene tutte le variabili da personalizzare per far funzionare il sistema 

## Testa
Essendo un progetto in evoluzione, potrebbero esserci degli errori presenti nel codice, vi consigliamo di testare voi stessi se nella versione scaricata il codice è consistente oppure controllare che le modifiche in locale apportate non abbiano danneggiato il codice già presente
### Unit Tests
Nella cartella del progetto eseguite
``` bash
vendor/bin/phpunit tests/*
```
### Integration Tests
Nella cartella del progetto eseguite
``` bash
vendor/bin/codeception Acceptance
```

# API Reference

## Calendar API

Le calendar API sono raggiungibili tramite la pagina `calendar-helper`e sono le seguenti

### GET

#### get-events

| Action      | Descrizione                                                   | Parametro | Tipo Parametro |
|-------------|---------------------------------------------------------------|-----------|----------------|
| get-events  | Recupera tutti gli eventi dalla tabella `calendar_event`.      | -         | -              |

#### get-event

| Action      | Descrizione                                                        | Parametro | Tipo Parametro |
|-------------|--------------------------------------------------------------------|-----------|----------------|
| get-event   | Recupera un evento specifico dalla tabella `calendar_event` dato un ID. | id        | string         |

#### get-note

| Action      | Descrizione                                                                | Parametro | Tipo Parametro |
|-------------|----------------------------------------------------------------------------|-----------|----------------|
| get-note    | Recupera la descrizione di un evento dalla tabella `prenotazioni` dato un ID. | id        | string         |

#### get-coach-event

| Action            | Descrizione                                                                  | Parametro | Tipo Parametro |
|-------------------|------------------------------------------------------------------------------|-----------|----------------|
| get-coach-event   | Recupera tutti gli eventi dalla tabella `calendar_event` dove il coach allena tramite la sua mail. | coach     | string         |

#### get-matches

| Action        | Descrizione                                                         | Parametro | Tipo Parametro |
|---------------|---------------------------------------------------------------------|-----------|----------------|
| get-matches   | Recupera tutti gli eventi segnati come match dalla tabella `calendar_event`. | -         | -              |

#### get-event-info

| Action         | Descrizione                                                                | Parametro | Tipo Parametro |
|----------------|----------------------------------------------------------------------------|-----------|----------------|
| get-event-info | Recupera un evento specifico dalla tabella `prenotazioni` dato un ID.         | id        | string         |

#### get-cams

| Action     | Descrizione                                                          | Parametro | Tipo Parametro |
|------------|----------------------------------------------------------------------|-----------|----------------|
| get-cams   | Recupera le telecamere attive per un evento specifico dato un ID.     | id        | string         |

#### get-time

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| get-time   | Recupera la data e l'ora di un evento dato un ID.             | id        | string         |

#### get-user-type

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| get-user-type   | Recupera il tipo di account dell'utente data la sua mail.             | email      | string         |

#### get-society-event

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| get-society-event   | Recupera tutti gli eventi di una società data la mail del responsabile.             | email      | string         |

### Post

#### save-event

| Action      | Descrizione                                                                           | Parametro | Tipo Parametro    |
|-------------|---------------------------------------------------------------------------------------|-----------|------------------|
| save-event  | Salva un nuovo evento nella tabella `calendar_event` e `prenotazioni`.                   | groupId   | string (opzionale) |
|             |                                                                                       | allDay    | boolean (opzionale) |
|             |                                                                                       | startDate | string |
|             |                                                                                       | endDate   | string (opzionale) |
|             |                                                                                       | daysOfWeek | array (opzionale) |
|             |                                                                                       | startTime | string (opzionale) |
|             |                                                                                       | endTime   | string (opzionale) |
|             |                                                                                       | startRecur | string (opzionale) |
|             |                                                                                       | endRecur   | string (opzionale) |
|             |                                                                                       | url       | string (opzionale) |
|             |                                                                                       | society   | string |
|             |                                                                                       | sport     | string (opzionale) |
|             |                                                                                       | author     | string |
|             |                                                                                       | note      | string (opzionale) |
|             |                                                                                       | eventType | string (opzionale) |
|             |                                                                                       | cameras | string (opzionale) |

#### delete-event

| Action        | Descrizione                                                     | Parametro | Tipo Parametro |
|---------------|-----------------------------------------------------------------|-----------|----------------|
| delete-event  | Elimina un evento dalla tabella `calendar_event` dato un ID.    | id        | string         |

#### edit-event

| Action        | Descrizione                                                               | Parametro | Tipo Parametro |
|---------------|---------------------------------------------------------------------------|-----------|----------------|
| edit-event    | Modifica un evento esistente nella tabella `calendar_event` e `prenotazioni`. | id        | string         |
|               |                                                                           | groupId   | string (opzionale) |
|               |                                                                           | startDate | string |
|               |                                                                           | endDate   | string (opzionale) |
|               |                                                                           | startTime | string (opzionale) |
|               |                                                                           | endTime   | string (opzionale) |
|               |                                                                           | url       | string (opzionale) |
|               |                                                                           | society   | string |
|               |                                                                           | note      | string (opzionale) |

#### save-cams

| Action      | Descrizione                                                            | Parametro | Tipo Parametro |
|-------------|------------------------------------------------------------------------|-----------|----------------|
| save-cams   | Salva le telecamere da attivare per un evento specifico.                | id        | string         |
|             |                                                                        | cameras   | array          |

## Gestione squadre e società API

La Gestione squadre e società può essere fatta tramite la pagina `myteam-helper`e sono le seguenti:

### GET

#### get-teams

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| get-teams   | Recupera tutte le squadre registrate nel sistema.             |        |          |

#### get-team

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| get-team   | Recupera la squadra dal suo identificativo.             | id        | string         |

#### get-team-by-coach

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| get-team-by-coach   | Recupera la squadra da uno dei suoi allenatori.             | email        | string         |

#### get-players-by-team

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| get-players-by-team  | Recupera i giocatori di una squadra dall'ID di essa.             | id        | string         |

#### get-society-by-boss

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| get-society-by-boss   | Recupera la società dal responsabile che la gestisce.             | email        | string         |

### POST

#### delete-player

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| delete-player   | Elimina l'associazione giocatore-squadra data la sua mail             | email        | string         |

#### delete-staff

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| delete-staff   | Elimina l'associazione giocatore-squadra data la sua mail e quella del responsabile che ne fa richiesta            | email        | string         |
|             |                                                                           | email   | string          |

## Video 
Le API per la gestione dei video si trovano nel file `videoList-helper` e sono le seguenti
### Get
#### get-playlist

| Action     | Descrizione                                                 | Parametro | Tipo Parametro |
|------------|-------------------------------------------------------------|-----------|----------------|
| get-playlist   | Ritorna la locazione di tutti i video in una sessione tramite uno di essi          | video        | string         |

#
