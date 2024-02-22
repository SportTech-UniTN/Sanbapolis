<!-- Javascripts per gestire il calendario -->
<script src="../js/calendar/calendar-scripts.js"></script>

<!-- PHP session init -->
<?php

include('../modals/calendar-header.php');
include_once("../modals/navbar.php");
include_once('../authentication/auth-helper.php');
include("./calendar-helper.php");

if (!isset($_COOKIE['email'])) {
    header("Location: ../authentication/login.php");
    exit();
}

$userType = $user['userType']; // Ottenere il tipo di utente dalla variabile $user['userType']

// Impostazioni predefinite
$delete = false;
$modify = false;
$add = false;

// Mappa il tipo di utente alle azioni e alla chiamata JavaScript
$actions = array(
    'allenatore' => array(
        'fetchFunction' => 'fetchCoachEvents',
        'args' => '"' . $user['email'] . '"',
        'add' => true,
        'delete'=> true,
    ),
    'società' => array(
        'fetchFunction' => 'fetchSocietyEvents',
        'args' => '"' . $user['email'] . '"',
        'add' => true,
        'delete'=> true,
    ),
    'manutentore' => array(
        'fetchFunction' => 'fetchEvents',
        'args' => '',
        'modify' => true,
        'add' => true,
        'delete'=> true,
    ),
    'tifoso' => array(
        'args' => '',
        'fetchFunction' => 'fetchMatches',
    ),
    // Tipo di utente non gestito
    'altro' => array(
        'fetchFunction' => 'fetchMatches',
    ),
);

if (isset($actions[$userType])) {
    $action = $actions[$userType];
    echo '<script>';
    echo $action['fetchFunction'] . '(' . $action['args'] . ');';
    echo '</script>';

    $delete = isset($action['delete']) ? $action['delete'] : $delete;
    $modify = isset($action['modify']) ? $action['modify'] : $modify;
    $add = isset($action['add']) ? $action['add'] : $add;
}
?>

<style>
    /* Style for the time inputs */
    input[type="time"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    /* Style for the input fields */
    input[type="date"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    .day-icon {
        width: 20px;
        height: 20px;
        margin-right: 5px;
    }

    /* Style for the circle icon */
    .circle-icon {
        width: 14px;
        height: 14px;
        background: url('https://api.iconify.design/cil:circle.svg') no-repeat center center;
        background-size: cover;
        display: inline-block;
        position: relative;
        top: 5px;
        /* Adjust the vertical position as needed */
        left: 5px;
        /* Adjust the horizontal position as needed */
    }

    /* Hide the actual checkboxes */
    .day-checkbox input[type="checkbox"] {
        display: none;
    }

    /* Style for the checked icons */
    .day-checkbox input[type="checkbox"]:checked+.day-icon {
        filter: grayscale(0%);
    }
</style>

<!-- Calendario "FullCalendar" caricato da JavaScript -->
<div class="container">
    <div id="alert"></div>
    <div id="calendar"></div>
</div>

<!-- Modale aggiunta nuovo evento -->
<div id="add-event-modal" class="white-popup-block mfp-hide">
    <p style="height: 30px; background: #8FB3FF; width: 100%;"></p>
    <h2>Nuovo Evento</h2>
    <div style="min-height: 250px;">
        <form id="save-form">
            <input type="hidden" name="id" />
            Societa:
            <select name="society" required>
                <option value="" disabled selected>Scegli una societa</option>
                <?php echo getSocieties(); ?>
            </select>
            Evento:
            <select name="event_type">
                <option value="training">Allenamento</option>
                <option value="match">Partita</option>
            </select><br>
            Data inizio: <input id="start-date" type="date" name="start-date" placeholder="Data inizio" autocomplete="off" value="<?= date('Y-m-d') ?>" required /><br>
            Data fine: <input id="end-date" type="date" name="end-date" placeholder="Data fine" autocomplete="off" value="<?= date('Y-m-d') ?>" required /><br>
            Ora inizio: <input type="time" name="startTime" placeholder="Ora inizio" /><br>
            Ora fine: <input type="time" name="endTime" placeholder="Ora fine" /><br>

            <!-- Scelta camere -->
            <div>
                <input type="checkbox" id="camera-checkbox" name="camera-checkbox" onchange="toggleCameraOptions(this)" />
                <label for="camera-checkbox">Seleziona telecamere</label>
            </div>
            <div id="camera-options" style="display: none;">
                <label> <input type="checkbox" name="camera[]" value="1"> Camera 1 </label>
                <label> <input type="checkbox" name="camera[]" value="2"> Camera 2 </label>
                <label> <input type="checkbox" name="camera[]" value="3"> Camera 3 </label>
                <label> <input type="checkbox" name="camera[]" value="4"> Camera 4 </label>
                <label> <input type="checkbox" name="camera[]" value="5"> Camera 5 </label>
                <label> <input type="checkbox" name="camera[]" value="6"> Camera 6 </label>
                <label> <input type="checkbox" name="camera[]" value="7"> Camera 7 </label>
                <label> <input type="checkbox" name="camera[]" value="8"> Camera 8 </label>
                <label> <input type="checkbox" name="camera[]" value="9"> Camera 9 </label>
                <label> <input type="checkbox" name="camera[]" value="10"> Camera 10 </label>
                <label> <input type="checkbox" name="camera[]" value="11"> Camera 11 </label>
                <label> <input type="checkbox" name="camera[]" value="12"> Camera 12 </label>
            </div>

            <!-- Ripetizione settimanale -->
            Ripetizione settimanale:<br>
            <input type="checkbox" name="repeatWeekly" onchange="toggleWeeklyRepeat(this)"> Si ripete ogni:<br>
            <div id="weeklyRepeat" style="display: none;">
                Data di inizio ripetizione: <input id="startRecur" type="date" name="startRecur" placeholder="Data di inizio ripetizione" autocomplete="off"><br>
                Data di fine ripetizione: <input id="endRecur" type="date" name="endRecur" placeholder="Data di fine ripetizione" autocomplete="off"><br>
                Giorni della settimana:<br>
                <input type="checkbox" name="daysOfWeek[]" value="1"> Lunedì<br>
                <input type="checkbox" name="daysOfWeek[]" value="2"> Martedì<br>
                <input type="checkbox" name="daysOfWeek[]" value="3"> Mercoledì<br>
                <input type="checkbox" name="daysOfWeek[]" value="4"> Giovedì<br>
                <input type="checkbox" name="daysOfWeek[]" value="5"> Venerdì<br>
                <input type="checkbox" name="daysOfWeek[]" value="6"> Sabato<br>
                <input type="checkbox" name="daysOfWeek[]" value="0"> Domenica<br>
            </div>

            <!-- Altre opzioni -->
            <label><input type="checkbox" name="showMoreOptions" onchange="toggleMoreOptions(this)"> Altre opzioni</label><br>
            <div id="moreOptions" style="display: none;">
                Note:<br>
                <textarea cols="55" rows="5" name="description" placeholder="Note"></textarea><br>
                Url: <input type="text" name="url" placeholder="Url"><br>
                GroupId: <input type="value" name="groupId" placeholder="GroupId"><br>
                Tutto il giorno: <input type="checkbox" name="allDay" placeholder="allday"><br>
            </div>

            <button type="button" id="save-event" onclick="confirmSaveEvent('<?php echo $_COOKIE['email']; ?>')">Salva</button>
        </form>
    </div>
    <div id="error-message" style="color: red; display: none;">Si prega di compilare tutti i campi obbligatori.</div>
</div>

<!-- Modale per visualizzare le informazioni dell'evento-->
<div id="show-event-modal" class="white-popup-block mfp-hide">
    <p style="height: 30px; background: orangered; width: 100%;"></p>
    <div style="display: flex;">
        <h2 id="event-date" style="flex: 1;">Giorno Mese Anno</h2>
        <p id="event-time-init" style="margin-left: 10px; font-size: 24px;">Orario Inizio</p>
        <p id="event-time-spacer" style="margin-left: 10px; font-size: 24px;">-</p>
        <p id="event-time-end" style="margin-left: 10px; font-size: 24px;">Orario Fine</p>
    </div>
    <div style="min-height: 250px;">
        <h3 id="event-name">Titolo</h3>
        <p id="event-note">Note evento</p>
        <p id="event-id" style="display: none;"> id </p>
    </div>

    <?php


    if ($delete) {
        echo ('<button id="delete-button" class="btn btn-danger" onclick="confirmdeleteEvent()">Elimina</button>');
    }
    if ($modify) {
        echo ('<button id="edit-button" class="btn btn-primary" onclick="ShowForEditEvent()">Modifica</button>');
    }
    if ($add) {
        echo ('<button id="add-button" class="btn btn-primary" onclick="Showcameras()">Imposta Camere</button>');
    }
    ?>
</div>

<!-- Modale modifica evento -->
<div id="modify-event-modal" class="white-popup-block mfp-hide">
    <p style="height: 30px; background: #8FB3FF; width: 100%;"></p>
    <h2 id=nome-evento>Nome Evento</h2>
    <div style="min-height: 250px;">
        <form id="edit-form">
            <input type="text" id="id-edit" style="display: none;"></p>
            <select name="society-edit" required>
                <option value="" id="selected-option" selected>Scegli una società</option>
                <?php echo getSocieties(); ?>
            </select>
            <label for="start-date-edit">Data inizio:</label>
            <input id="start-date-edit" type="date" name="start-date-edit" placeholder="Data inizio" autocomplete="off" required /><br>
            <label for="end-date-edit">Data fine:</label>
            <input id="end-date-edit" type="date" name="end-date-edit" placeholder="Data fine" autocomplete="off" required /><br>
            <label for="start-time-edit">Ora inizio:</label>
            <input type="time" name="start-time-edit" id="start-time-edit" placeholder="Ora inizio" /><br>
            <label for="end-time-edit">Ora fine:</label>
            <input type="time" name="end-time-edit" id="end-time-edit" placeholder="Ora fine" /><br>
            <textarea cols="55" rows="5" name="description-edit" id="description-edit" placeholder="Note"></textarea><br>
            <label for="url-edit">Url:</label>
            <input type="text" name="url-edit" id="url-edit" placeholder="Url"><br>
            <label for="group-id-edit">GroupId:</label>
            <input type="text" name="group-id-edit" id="group-id-edit" placeholder="GroupId"><br>
            <button type="button" id="save-event" onclick="editEvent()">Salva</button>
        </form>
    </div>
    <div id="error-message" style="color: red; display: none;">Si prega di compilare tutti i campi obbligatori.</div>
</div>

<!-- Modal scelta camere  -->
<div id="choose-cams" class="white-popup-block mfp-hide">
    <p style="height: 30px; background: orangered; width: 100%;"></p>
    <div class="modal-content">
        <p type="text" id="id-cams" style="display: none;"> id </p>
        <h2>Seleziona le telecamere da attivare:</h2>
        <form id="cameraForm">
            <label> <input type="checkbox" name="camera[]" value="1"> Camera 1 </label>
            <label> <input type="checkbox" name="camera[]" value="2"> Camera 2 </label>
            <label> <input type="checkbox" name="camera[]" value="3"> Camera 3 </label>
            <label> <input type="checkbox" name="camera[]" value="4"> Camera 4 </label>
            <label> <input type="checkbox" name="camera[]" value="5"> Camera 5 </label>
            <label> <input type="checkbox" name="camera[]" value="6"> Camera 6 </label>
            <label> <input type="checkbox" name="camera[]" value="7"> Camera 7 </label>
            <label> <input type="checkbox" name="camera[]" value="8"> Camera 8 </label>
            <label> <input type="checkbox" name="camera[]" value="9"> Camera 9 </label>
            <label> <input type="checkbox" name="camera[]" value="10"> Camera 10 </label>
            <label> <input type="checkbox" name="camera[]" value="11"> Camera 11 </label>
            <label> <input type="checkbox" name="camera[]" value="12"> Camera 12 </label>
            <button type="submit" onclick="saveCameras()">Attiva</button>
        </form>
    </div>
</div>


</div>
</div>

<?php

include('../modals/footer.php');

?>