<!-- PHP session init -->
<?php

include('../modals/header.php');
include_once("../modals/navbar.php");
include_once('../authentication/auth-helper.php');

if (!isset($_COOKIE['email'])) {
  header("Location: ../authentication/login.php");
  exit();
}

?>

<!-- Javascripts per gestire la pagina squadra -->
<script src="../js/teams/myteam-scripts.js"></script>

<script>
  var societyPromise = getSocietyByBoss("<?php echo $user['email']; ?>");
  societyPromise
    .then(function(society) {
      // Elabora la società ottenuta (successo)
      return getCoachesByBoss("<?php echo $user['email']; ?>");
    })
</script>


<div class="container">
  <h2 id="society-name">My society Name</h2>
  <p class="text-left" id="society-code">Code</p>


  <!-- Form di inviti -->
  <div>
    <form id="invite-email-form" action="../modals/email-handler.php" method="post">
      <div class="form-group">
        <label for="email">Invita tramite indirizzo email</label>
        <input type="email" class="form-control" id="invited-email" name="invited-email" placeholder="Inserisci l'indirizzo email">
        <!-- Aggiungi un campo nascosto per memorizzare il nome del society -->
        <input type="hidden" name="hidden-society-name" value="My society Name">
        <input type="hidden" name="hidden-society-code" value="code">
      </div>
      <button type="submit" class="btn btn-primary">Invita</button>
    </form>
  </div>

  <!-- Aggiunto CSS temporaneo per non far flashare al caricamento della pagina le carte se la squadra è vuota -->
  <style>
    .row {
      display: none;
    }
  </style>

  <!-- Partecipanti -->
  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <img src="../assets/profileimg/beard.png" class="card-img-top" alt="Card Image">
        <div class="card-body">
          <h5 class="card-title">Card Title 1</h5>
          <button class="btn btn-danger text-right" onclick="deleteStaff(this, '<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>')">Elimina</button>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <img src="../assets/profileimg/beard.png" class="card-img-top" alt="Card Image">
        <div class="card-body">
          <h5 class="card-title">Card Title 2</h5>
          <button class="btn btn-danger text-right" onclick="deleteStaff(this, '<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>')">Elimina</button>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <img src="../assets/profileimg/beard.png" class=" card-img-top" alt="Card Image">
        <div class="card-body">
          <h5 class="card-title">Card Title 3</h5>
          <button class="btn btn-danger text-right" onclick="deleteStaff(this, '<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>')">Elimina</button>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <img src="../assets/profileimg/beard.png" class="card-img-top" alt="Card Image">
        <div class="card-body">
          <h5 class="card-title">Card Title 4</h5>
          <button class="btn btn-danger text-right" onclick="deleteStaff(this, '<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>')">Elimina</button>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <img src="../assets/profileimg/beard.png" class="card-img-top" alt="Card Image">
        <div class="card-body">
          <h5 class="card-title">Card Title 5</h5>
          <button class="btn btn-danger text-right" onclick="deleteStaff(this, '<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>')">Elimina</button>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <img src="../assets/profileimg/beard.png" class="card-img-top" alt="Card Image">
        <div class="card-body">
          <h5 class="card-title">Card Title 6</h5>
          <button class="btn btn-danger text-right" onclick="deleteStaff(this, '<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>')">Elimina</button>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <img src="../assets/profileimg/beard.png" class="card-img-top" alt="Card Image">
        <div class="card-body">
          <h5 class="card-title">Card Title 7</h5>
          <button class="btn btn-danger text-right" onclick="deleteStaff(this, '<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>')">Elimina</button>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <img src="../assets/profileimg/beard.png" class="card-img-top" alt="Card Image">
        <div class="card-body">
          <h5 class="card-title">Card Title 8</h5>
          <button class="btn btn-danger text-right" onclick="deleteStaff(this, '<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>')">Elimina</button>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <img src="../assets/profileimg/beard.png" class="card-img-top" alt="Card Image">
        <div class="card-body">
          <h5 class="card-title">Card Title 9</h5>
          <button class="btn btn-danger text-right" onclick="deleteStaff(this, '<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>')">Elimina</button>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <img src="../assets/profileimg/beard.png" class="card-img-top" alt="Card Image">
        <div class="card-body">
          <h5 class="card-title">Card Title 10</h5>
          <button class="btn btn-danger text-right" onclick="deleteStaff(this, '<?php echo isset($_COOKIE['email']) ? $_COOKIE['email'] : ''; ?>')">Elimina</button>
        </div>
      </div>
    </div>
  </div>
</div>


</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>