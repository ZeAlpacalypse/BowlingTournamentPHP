window.onload = function () {
  document.getElementById("viewTeamsBtn").addEventListener("click", showTeams);
  document
    .getElementById("viewTeamInfo")
    .addEventListener("click", showTeamInfo);
  document.getElementById("gameRecap").addEventListener("click", showRecap);
};

function showTeams() {
  clearSelections();
  document.querySelector(".teams").classList.add("visible");
  document.querySelector(".players").classList.remove("visible");

  getAllTeams();
  document
    .querySelector("#teamsTable")
    .addEventListener("click", handleRowClick);
}

function showTeamInfo() {
  document.querySelector(".teams").classList.remove("visible");
  document.querySelector(".players").classList.add("visible");
  getAllPlayers();
  document
    .querySelector("#playersTable")
    .addEventListener("click", handleRowClick);
}
function showRecap() {
  document.querySelector(".players").classList.remove("visible");
  document.querySelector(".gameRecap").classList.add("visible");
  getAllGames();
  document
    .querySelector("#gameRecapTable")
    .addEventListener("click", showRecap);
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
function getAllPlayers() {
  let url = "playerService/players";
  let method = "GET";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (xhr.status === 200) {
        if (resp.data) {
          buildTeam(resp.data);
        } else if (xhr.status === 500) {
          alert("Server Error: " + resp.error);
        }
      }
    }
  };
  xhr.open(method, url, true);
  xhr.send();
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
          buildGames(resp.data);
        } else if (xhr.status === 500) {
          alert("Server Error: " + resp.error);
        }
      }
    }
  };
  xhr.open(method, url, true);
  xhr.send();
}
// text is a JSON string containing an array
function buildTable(text) {
  let arr = JSON.parse(text); // get JS Objects
  let html = "";
  //"<table><tr><th>Game ID</th><th>Age Rating</th><th>Title</th><th>Price</th><th>Digital?</th></tr>";
  for (let i = 0; i < arr.length; i++) {
    let row = arr[i];
    html += "<tr>";
    html += "<td>" + row.teamID + "</td>";
    html += "<td>" + row.teamName + "</td>";
    html += "</tr>";
  }
  html += "</table>";
  let theTable = document.querySelector("#teamsTable");
  theTable.innerHTML = html;
}
function buildTeam(text) {
  let arr = JSON.parse(text);
  let row = document.querySelector(".selected");
  let id = Number(row.querySelectorAll("td")[0].innerHTML);
  let html =
    "<table><tr><th>Player ID</th><th>Player Name</th><th>Hometown</th><th>Province</th></tr>";
  for (let i = 0; i < arr.length; i++) {
    let player = arr[i];
    if (player.teamID === id) {
      html += "<tr>";
      html += "<td>" + player.playerID + "</td>";
      html += "<td>" + player.firstName + " " + player.lastName + "</td>";
      html += "<td>" + player.homeTown + "</td>";
      html += "<td>" + player.provinceCode + "</td>";
      html += "</tr>";
    }
  }
  html += "</table>";
  let theTable = document.querySelector("#playersTable");
  theTable.innerHTML = html;
}
function buildGames(text) {
  let arr = JSON.parse(text);
  let row = document.querySelector(".selected");
  let id = Number(row.querySelectorAll("td")[0].innerHTML);
  console.log(id);
  let html =
    "<table><tr><th>Game ID</th><th>Player ID</th><th>Score</th><th>Game State</th></tr>";
  for (let i = 0; i < arr.length; i++) {
    let game = arr[i];
    if (game.teamID === id) {
      html += "<tr>";
      html += "<td>" + game.gameID + "</td>";
      html += "<td>" + game.playerID + "</td>";
      html += "<td>" + game.score + "</td>";
      html += "<td>" + game.gameStateID + "</td>";
      html += "</tr>";
    }
  }
  html += "</table>";
}

function handleRowClick(evt) {
  clearSelections();
  evt.target.parentElement.classList.add("selected");
}

function clearSelections() {
  let trs = document.querySelectorAll("tr");
  for (let i = 0; i < trs.length; i++) {
    trs[i].classList.remove("selected");
  }
}
