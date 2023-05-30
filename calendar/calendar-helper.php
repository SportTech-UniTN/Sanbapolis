<?php

use JetBrains\PhpStorm\Internal\ReturnTypeContract;

require_once '../authentication/db_connection.php';

/**
 * Verifica se la richiesta corrente è una richiesta AJAX.
 *
 * @return bool True se la richiesta è una richiesta AJAX, altrimenti False.
 */
function is_ajax_request()
{
    // Verifica se l'intestazione 'HTTP_X_REQUESTED_WITH' è presente e ha il valore 'xmlhttprequest'
    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {
        return true;
    }

    // La richiesta non è una richiesta AJAX
    return false;
}


/**
 * Elimina un training specifico dalla tabella "calendar_events" e le righe figlie correlate nella tabella "event_info".
 *
 * @param int $event_id L'ID dell'evento da eliminare.
 * @return bool True se l'eliminazione ha avuto successo per entrambe le tabelle, altrimenti False.
 */
function delete_training($event_id)
{
    $con = get_connection();

    // Elimina le righe figlie nella tabella "event_info"
    $sql_delete_event_info = "DELETE FROM event_info WHERE event_id = ?";
    $query_delete_event_info = $con->prepare($sql_delete_event_info);
    $query_delete_event_info->execute([$event_id]);

    // Elimina l'evento dalla tabella "calendar_events"
    $sql_delete_event = "DELETE FROM calendar_events WHERE id = ?";
    $query_delete_event = $con->prepare($sql_delete_event);
    $query_delete_event->execute([$event_id]);

    // Verifica se le query di eliminazione hanno avuto successo
    if ($query_delete_event_info->rowCount() > 0 && $query_delete_event->rowCount() > 0) {
        return true;
    } else {
        // Almeno una delle eliminazioni non è riuscita
        return false;
    }
}

/**
 * Salva un training nella tabella "calendar_events" insieme alle informazioni correlate nella tabella "event_info".
 * Inoltre gestisce i parametri di fullcalendar.io che non sono al momento utilizzati
 *
 * @param int $groupId L'ID del gruppo associato all'evento.
 * @param bool $allDay Indica se l'evento dura per l'intera giornata.
 * @param string $startDate La data di inizio dell'evento.
 * @param string $endDate La data di fine del training.
 * @param string|null $daysOfWeek I giorni della settimana in cui si ripete l'evento.
 * @param string|null $startTime L'orario di inizio del training.
 * @param string|null $endTime L'orario di fine del training.
 * @param string|null $startRecur La data di inizio della ricorrenza del training.
 * @param string|null $endRecur La data di fine della ricorrenza del training.
 * @param string $url L'URL associato al training.
 * @param string $society Il nome dell'associazione/società associata al training.
 * @param string $sport Lo sport del training.
 * @param string $coach Il nome dell'allenatore associato al training.
 * @param string $note La nota relativa al training.
 * @param string $eventType Il tipo di evento (es. "match" o altro).
 * @param int|null $id L'ID del training da modificare se operazione di modifica.
 * @return int L'ID del training salvato.
 */
function save_training($groupId, $allDay, $startDate, $endDate, $daysOfWeek, $startTime, $endTime, $startRecur, $endRecur, $url, $society, $sport, $coach, $note, $eventType, $id)
{
    // missing premium parameter `resourceEditable`=?, `resourceId`=?, `resourceIds`=?

    $con = get_connection();

    if ($startRecur == "0000-00-00" || $startRecur == null) {
        $startRecur = $startDate;
    }

    if ($endRecur == "0000-00-00" || $endRecur == null) {
        // +1 perché altrimenti non prende giorno finale
        $endRecursive = strtotime($endDate . ' +1 day');
        $endRecur =  date('Y-m-d', $endRecursive);
    }

    // allDay settings
    if ($allDay) {
        $startTime = null;
        $endTime = null;
        $allDay = 1;
    } else {
        $allDay = 0;
    }

    if ($daysOfWeek == "null") {
        $daysOfWeek = null;
    }

    $interactive = true;
    $className = null;

    //disabilitata per aggiungere la gestione della chiamata al db per modificare i valori
    $editable = true;
    $startEditable = false;
    $durationEditable = false;

    $display = true;

    // nella palestra non possono esserci eventi contemporanei
    $overlap = false;

    //$color settato a null perch modifichiamo bordi e background in base al tipo di evento
    $color = null;
    $backgroundColor = getEventColor($sport);
    $textcolor = "white";


    $eventTypeBoolean = ($eventType === 'match') ? 0 : 1;

    if ($eventTypeBoolean) {
        $title = $society;
        $borderColor = $backgroundColor;
    } else {
        $title = $society . ' | ' . $eventType;
        $borderColor = "black";
    }

    //se presente id update delle due tabelle
    if ($id) {
        $sql = "UPDATE calendar_events SET `groupId`=?, `allDay`=?, `start`=?, `end`=?, `daysOfWeek`=?, `startTime`=?, `endTime`=?,`startRecur`=?, `endRecur`=?, `title`=?, `url`=?, 
        `interactive`=?, `className`=?, `editable`=?, `startEditable`=? , `durationEditable`=?, `display`=?, `overlap`=?, `color`=?, `backgroundColor`=?, `borderColor`=?, `textColor`=? 
        WHERE id=?";
        $query = $con->prepare($sql);
        $query->execute([
            $groupId, $allDay, $startDate, $endDate, $daysOfWeek, $startTime, $endTime, $startRecur, $endRecur, $title, $url,
            $interactive, $className, $editable, $startEditable, $durationEditable, $display, $overlap, $color, $backgroundColor, $borderColor, $textcolor,
            $id
        ]);

        $sql = "UPDATE event_info SET  `society`=?, `sport`=?, `coach`=?, `note`=? , `training`=? , WHERE id=?";
        $query = $con->prepare($sql);
        $query->execute([$society, $sport, $coach, $note, $eventTypeBoolean, $id]);

        return $id;
    } else { //se non presente id inseriamo i record delle due tabelle

        $sql = "INSERT INTO calendar_events (`groupId`,`allDay`,`start`,`end`,`daysOfWeek`, `startTime`, `endTime`,`startRecur`, `endRecur`, `title`, `url`,
        `interactive`, `className`, `editable`, `startEditable`, `durationEditable`, `display`, `overlap`, `color`, `backgroundColor`, `borderColor`, `textColor`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $query = $con->prepare($sql);
        $query->execute([
            $groupId, $allDay, $startDate, $endDate, $daysOfWeek, $startTime, $endTime, $startRecur, $endRecur, $title, $url,
            $interactive, $className, $editable, $startEditable, $durationEditable, $display, $overlap, $color, $backgroundColor, $borderColor, $textcolor
        ]);

        $calendar_id = $con->lastInsertId();
        $sql = "INSERT INTO event_info (`society`, `sport`, `coach`, `note`, `training`, `event_id`) 
        VALUES (?,?,?,?,?,?)";
        $query = $con->prepare($sql);
        $query->execute([$society, $sport, $coach, $note, $eventTypeBoolean, $calendar_id]);

        return $calendar_id;
    }
}

/**
 * Recupera tutti gli eventi dalla tabella "calendar_events" del database e li restituisce come JSON.
 *
 * @return string Una stringa JSON che rappresenta gli eventi. Se non ci sono eventi, la stringa JSON sarà vuota.
 */
function getEvents()
{
    $con = get_connection();
    $query = "SELECT * FROM calendar_events";
    $statement = $con->query($query);
    $events = $statement->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($events);
}

/**
 * Ottiene le informazioni di un singolo evento dalla tabella "calendar_events" in base all'ID fornito e lo restituisce come JSON.
 *
 * @param int $id L'ID dell'evento da recuperare.
 * @return string Una stringa JSON che rappresenta l'evento. Se l'evento non viene trovato, la stringa JSON sarà vuota.
 */
function getEvent($id)
{
    $con = get_connection();
    $query = "SELECT * FROM calendar_events WHERE id = :id";
    $statement = $con->prepare($query);
    $statement->bindParam(':id', $id);
    $statement->execute();
    $event = $statement->fetch(PDO::FETCH_ASSOC);
    return json_encode($event);
}

/**
 * Recupera gli incontri dal database.
 * @return string JSON contenente gli incontri recuperati dal database.
*/
function getMatches()
{
    $con = get_connection();
    $query = "SELECT ce.* FROM calendar_events ce INNER JOIN event_info ei ON ce.id = ei.event_id WHERE ei.training = 0";
    $statement = $con->query($query);
    $events = $statement->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($events);
}

/**
 * Recupera gli eventi per un allenatore specifico dal database.
 * @param string $coach Il nome o l'identificatore dell'allenatore.
 * @return string Stringa JSON contenente gli eventi dell'allenatore.
 */
function getCoachEvents($coach)
{
    $con = get_connection();
    $query = "SELECT ce.* FROM calendar_events ce INNER JOIN event_info ei ON ce.id = ei.event_id WHERE ei.coach = :coach";
    $statement = $con->prepare($query);
    $statement->bindParam(':coach', $coach);
    $statement->execute();
    $events = $statement->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($events);
}

/**
 * Recupera la nota associata a un evento dalla tabella "event_info" del database, in base all'ID dell'evento fornito.
 *
 * @param int $id L'ID dell'evento per il quale si desidera recuperare la nota.
 * @return array Un array associativo contenente la nota dell'evento. Se la nota non viene trovata, l'array sarà vuoto.
 */
function getNote($id)
{
    $con = get_connection();
    $query = "SELECT note FROM event_info WHERE event_id = :id";
    $statement = $con->prepare($query);
    $statement->bindParam(':id', $id);
    $statement->execute();
    $note = $statement->fetch(PDO::FETCH_ASSOC);
    return $note;
}

/**
 * Ottiene il colore associato a uno specifico sport.
 *
 * @param string $sport Lo sport per il quale si desidera ottenere il colore.
 * @return string Una stringa che rappresenta il colore corrispondente allo sport specificato. Se lo sport non corrisponde a nessuna delle opzioni predefinite, viene restituito il colore di default.
 */
function getEventColor($sport)
{
    if ($sport == 'calcio') {
        return "purple";
    } else if ($sport == 'pallavolo') {
        return "darkorange";
    } else if ($sport == 'basket') {
        return "darkgreen";
    }
    return '#378006';
}

///////////////////////////
// GET e POST Management //
///////////////////////////

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'save-event') { // salvataggio di un evento
        //tabella calendar_event
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $groupId = isset($_POST['groupId']) ? $_POST['groupId'] : null;
        $allDay = isset($_POST['allDay']) ? $_POST['allDay'] : null;
        $startDate = isset($_POST['start-date']) ? $_POST['start-date'] : null;
        $endDate = isset($_POST['end-date']) ? $_POST['end-date'] : null;
        $daysOfWeek = isset($_POST['daysOfWeek']) ? $_POST['daysOfWeek'] : null;
        $startTime = isset($_POST['startTime']) ? $_POST['startTime'] : null;
        $endTime = isset($_POST['endTime']) ? $_POST['endTime'] : null;
        $startRecur = isset($_POST['startRecur']) ? $_POST['startRecur'] : null;
        $endRecur = isset($_POST['endRecur']) ? $_POST['endRecur'] : null;
        $url = isset($_POST['url']) ? $_POST['url'] : null;
        // parsato daysOfWeek in JSON in modo da salvarlo nel database come stringa
        $daysOfWeek = json_encode($daysOfWeek);

        //tabella event-info 
        $society = isset($_POST['society']) ? $_POST['society'] : null;
        $sport = isset($_POST['sport']) ? $_POST['sport'] : null;
        $coach = isset($_POST['coach']) ? $_POST['coach'] : null;
        $note = isset($_POST['description']) ? $_POST['description'] : null;
        $eventType = isset($_POST['event_type']) ? $_POST['event_type'] : null;

        //Sono obbligatori society e startdate ed effettuiamo il controllo che esistano
        if ($society && $startDate) {
            $id = save_training($groupId, $allDay, $startDate, $endDate, $daysOfWeek, $startTime, $endTime, $startRecur, $endRecur, $url, $society, $sport, $coach, $note, $eventType, $id);
            echo json_encode(array('status' => 'success', 'id' => $id));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Missing required fields'));
        }
    } elseif ($action == 'get-events') { // recupero tutti gli eventi da calendar_event
        header('Content-Type: application/json');
        echo getEvents();
    } elseif ($action == 'get-event') { // recupero evento da calendar_event con id specifico
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        header('Content-Type: application/json');
        if ($id) {
            echo getEvent($id);
        }
    } elseif ($action == 'get-note') { // recupero descrizione evento (se esiste) da event_info
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id) {
            $note = getNote($id);
            if ($note) {
                echo $note['note'];
            } else {
                return " ";
            }
        }
    } elseif ($action == 'get-coach-event') { //recupero tutti gli eventi da calendar_event dove allena coach
        $coach = isset($_GET['coach']) ? $_GET['coach'] : null;
        if ($coach) {
            header('Content-Type: application/json');
            echo getCoachEvents($coach);
        }
    } elseif ($action == 'delete-event') { // Elimino evento con quell'id
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        if (delete_training($id)) {
            $response = array('status' => 'success', 'message' => 'Evento eliminato con successo');
        } else {
            $response = array('status' => 'error', 'message' => 'Richiesta non valida');
        }

        echo json_encode($response);
    } elseif ($action == 'get-matches') { // recupero tutti gli eventi segnati come match
        header('Content-Type: application/json');
        echo getMatches();
    } else {
        // Invalid action
    }
} else {
    // Missing 'action' parameter
}

?>