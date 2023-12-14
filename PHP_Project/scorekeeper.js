window.onload = function () {
  document
    .querySelector("#viewSchedule")
    .addEventListener("click", showSchedule);
  document.querySelector("#scoreGames").addEventListener("click", showScore);
  getAllTeams();
  getAllPlayers();
  document
    .querySelector("#teamOption")
    .addEventListener("change", filterPlayersByTeam);
};
function showSchedule() {
  document.querySelector(".schedule-data").classList.add("visible");
  document.querySelector(".score-data").classList.remove("visible");
  getAllGames();
}

function showScore() {
  document.querySelector(".schedule-data").classList.remove("visible");
  document.querySelector(".score-data").classList.add("visible");
}

function getAllGames() {
  let url = "gameService/games";
  let method = "GET";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (xhr.status === 200) {
        if (resp.data) {
          buildTable(resp.data);
        } else if (xhr.status === 500) {
          alert("Server Error: " + resp.error);
        }
      }
    }
  };
  xhr.open(method, url, true);
  xhr.send();
}

function getAllTeams() {
  let url = "teamService/teams";
  let method = "GET";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (xhr.status === 200) {
        if (resp.data) {
          buildTeamOption(resp.data);
        } else if (xhr.status === 500) {
          alert("Server Error: " + resp.error);
        }
      }
    }
  };
  xhr.open(method, url, true);
  xhr.send();
}
function getAllPlayers() {
  let url = "playerService/players";
  let method = "GET";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (xhr.status === 200) {
        if (resp.data) {
          buildPlayerOption(resp.data);
        } else if (xhr.status === 500) {
          alert("Server Error: " + resp.error);
        }
      }
    }
  };
  xhr.open(method, url, true);
  xhr.send();
}
function buildTeamOption(text) {
  let arr = JSON.parse(text);
  let html = '<select id="teamNames">';
  for (let i = 0; i < arr.length; i++) {
    let row = arr[i];
    html += "<option value='" + row.teamID + "'>" + row.teamName + "</option>";
  }
  html += "</select>";
  let dropDownBox = document.querySelector("#teamOption");
  dropDownBox.innerHTML = html;
}
function buildPlayerOption(text) {
  let arr = JSON.parse(text);
  let html = '<select id="teamMembers">';
  for (let i = 0; i < arr.length; i++) {
    let row = arr[i];
    html +=
      "<option value='" +
      row.playerID +
      "' data-teamid='" +
      row.teamID +
      "'>" +
      row.firstName +
      " " +
      row.lastName +
      "</option>";
  }
  html += "</select>";
  let dropDownBox = document.querySelector("#playerOption");
  dropDownBox.innerHTML = html;
}
// text is a JSON string containing an array
function buildTable(text) {
  let arr = JSON.parse(text); // get JS Objects
  let html =
    "<table><tr><th>Match ID</th><th>Game Number</th><th>Game State</th><th>Score</th><th>Player ID</th></tr>";

  for (let i = 0; i < arr.length; i++) {
    let row = arr[i];
    if (row.gameStateID === "AVAILABLE") {
      html += "<tr>";
      html += "<td>" + row.matchID + "</td>";
      html += "<td>" + row.gameNumber + "</td>";
      html += "<td>" + row.gameStateID + "</td>";
      html += "<td>" + row.score + "</td>";
      html += "<td>" + row.playerID + "</td>";
      html += "</tr>";
    }
  }
  html += "</table>";

  let theTable = document.querySelector("#scheduleTable");
  theTable.innerHTML = html;
}
function filterPlayersByTeam() {
  let teamID = document.querySelector("#teamNames").value;
  let playerOptions = document.querySelectorAll("#teamMembers option");

  playerOptions.forEach((option) => {
    let playerTeamID = option.getAttribute("data-teamid");
    if (playerTeamID === teamID) {
      option.style.display = "block";
    } else {
      option.style.display = "none";
    }
  });
}
