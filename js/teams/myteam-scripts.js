//////////////////////////
// Chiamate al database //
//////////////////////////


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

function getTeambyCoach(coach) {
    $.ajax({
        url: 'http://localhost/profile/myteam-helper.php?action=get-team-by-coach',
        method: 'GET',
        data: {
            coach: coach
        },
        dataType: "json",
        success: function (response) {
            updateMyTeampage(response.team);
        },
        error: function (xhr, status, error) {
            console.log(error);
        }
    });
}


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

function updateMyTeampage(team) {
    var teamNameElement = document.getElementById("team-name");
    if (teamNameElement) {
        teamNameElement.textContent = team.nome;
    }

    var teamCodeElement = document.getElementById("team-code");
    if (teamCodeElement) {
        teamCodeElement.textContent = team.code_players;
    }

}

function updateCardVisibility(players) {
    var n_players = players.length;
    var conditions = [];

    for (var i = 0; i < n_players; i++) {
      conditions.push(true);
    }
  
    var cards = document.querySelectorAll('.col-md-4');
  
    for (var i = 0; i < cards.length; i++) {
      if (conditions[i]) {
        var cardTitle = cards[i].querySelector('.card-title');
        var email = players[i].email;
        console.log(email);
        cardTitle.textContent = email;
        cards[i].style.display = "block";
      } else {
        cards[i].style.display = "none";
      }
    }
  }
  