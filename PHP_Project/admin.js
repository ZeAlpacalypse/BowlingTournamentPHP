let addOrUpdate;

window.onload = function () {
  document
    .querySelector("#editTeams")
    .addEventListener("click", showTeamEditor);
  //document.querySelector("#setupRound").addEventListener("click", showRoundSetup);
  document.querySelector("#btnTeamAdd").addEventListener("click", addTeam);
  document
    .querySelector("#btnTeamDelete")
    .addEventListener("click", deleteTeam);
  document
    .querySelector("#btnTeamUpdate")
    .addEventListener("click", updateTeam);
  document
    .querySelector("#btnTeamDone")
    .addEventListener("click", processTeamCrud);
  document
    .querySelector("#btnTeamCancel")
    .addEventListener("click", hideTeamCrudPanel);
  document
    .querySelector(".team-editor")
    .addEventListener("click", handleRowClick);
  document
    .querySelector("#playerResults")
    .addEventListener("click", handlePlayerClick);
  document.querySelector("#btnPlayerAdd").addEventListener("click", addPlayer);
  document
    .querySelector("#btnPlayerDelete")
    .addEventListener("click", deletePlayer);
  document
    .querySelector("#btnPlayerUpdate")
    .addEventListener("click", updatePlayer);
  document
    .querySelector("#btnPlayerDone")
    .addEventListener("click", processPlayerCrud);
  document
    .querySelector("#btnPlayerCancel")
    .addEventListener("click", hidePlayerCrudPanel);
};

//team functions
function showTeamEditor() {
  document.querySelector(".team-editor").classList.add("visible");
  hideTeamCrudPanel();
  getAllTeams();
}

function getAllTeams() {
  document.querySelector("#teamCrudBtns").classList.remove("hidden");
  document.querySelector("#playerCrudBtns").classList.add("hidden");
  let url = "teamService/teams";
  let method = "GET";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (resp.data) {
        buildTable(resp.data);
        setDeleteUpdateButtonState(false);
      } else {
        alert(resp.error + "; status code: " + xhr.status);
      }
    }
  };
  xhr.open(method, url, true);
  xhr.send();
}

function buildTable(text) {
  let arr = JSON.parse(text); // get JS Objects
  let html = "<table>";
  for (let i = 0; i < arr.length; i++) {
    let row = arr[i];
    html += "<tr id='" + row.teamID + "' class='teamRow'>";
    html += "<td>" + row.teamID + "</td>";
    html += "<td>" + row.teamName + "</td>";
    html += "</tr>";
  }
  html += "</table>";
  let theTable = document.querySelector(".team-editor");
  theTable.innerHTML = html;
}

function addTeam() {
  addOrUpdate = "add";
  clearTeamCrudPanel();
  showTeamCrudPanel();
}

function updateTeam() {
  addOrUpdate = "update";
  populateTeamCrudPanel();
  showTeamCrudPanel();
}

function deleteTeam() {
  let row = document.querySelector(".selected");
  let id = Number(row.querySelectorAll("td")[0].innerHTML);

  let url = "teamService/teams/" + id;
  let method = "DELETE";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      console.log(resp);
      if (resp.data) {
        alert("Team deleted");
      } else {
        alert(resp.error + " status code: " + xhr.status);
      }
      getAllTeams();
    }
  };
  xhr.open(method, url, true);
  xhr.send();
}

function processTeamCrud() {
  let id = Number(document.querySelector("#teamID").value);
  let name = document.querySelector("#teamName").value;

  let obj = {
    teamID: id,
    teamName: name,
  };

  let url = "teamService/teams/" + id;
  let method = addOrUpdate === "add" ? "POST" : "PUT";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      console.log(resp);
      if (resp.data) {
        if (xhr.status === 200) {
          alert("Team updated.");
        } else if (xhr.status === 201) {
          alert("Team added.");
        }
      } else {
        alert(resp.error + " status code: " + xhr.status);
      }
      hideTeamCrudPanel();
      getAllTeams();
    }
  };
  xhr.open(method, url, true);
  xhr.send(JSON.stringify(obj));
}

function handleRowClick(evt) {
  let teamID = evt.target.parentElement.id;
  clearSelections();
  evt.target.parentElement.classList.add("selected");
  getPlayers(teamID);
  setDeleteUpdateButtonState(true);
}

function showTeamCrudPanel() {
  document.querySelector("#addUpdateTeamContainer").classList.remove("hidden");
}

function clearTeamCrudPanel() {
  document.querySelector("#teamID").value = "";
  document.querySelector("#teamName").value = "";
}

function hideTeamCrudPanel() {
  document.querySelector("#addUpdateTeamContainer").classList.add("hidden");
}

function populateTeamCrudPanel() {
  let selectedTeam = document.querySelector(".selected");
  let teamID = Number(selectedTeam.querySelector("td:nth-child(1)").innerHTML);
  let teamName = selectedTeam.querySelector("td:nth-child(2)").innerHTML;

  document.querySelector("#teamID").value = teamID;
  document.querySelector("#teamName").value = teamName;
}

//player functions
function getPlayers(teamID) {
  //document.querySelector("#teamCrudBtns").classList.add("hidden");
  document.querySelector("#playerCrudBtns").classList.remove("hidden");
  let url = "playerService/players";
  let method = "GET";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (resp.data) {
        displayPlayers(resp.data, teamID);
      } else {
        alert(resp.error + "; status code: " + xhr.status);
      }
    }
  };
  xhr.open(method, url, true);
  xhr.send();
}

function displayPlayers(players, teamID) {
  let arr = JSON.parse(players);
  let row;
  let html =
    "<table><tr><th>Player ID</th><th>Team ID</th><th>First Name</th><th>Last Name</th><th>Home Town</th><th>Province</th></tr>";
  for (let i = 0; i < arr.length; i++) {
    row = arr[i];
    if (row.teamID === Number(teamID)) {
      html += "<tr>";
      html += "<td>" + row.playerID + "</td>";
      html += "<td>" + row.teamID + "</td>";
      html += "<td>" + row.firstName + "</td>";
      html += "<td>" + row.lastName + "</td>";
      html += "<td>" + row.homeTown + "</td>";
      html += "<td>" + row.provinceCode + "</td>";
      html += "</tr>";
    }
  }
  html += "</table>";

  let thePlayers = document.querySelector("#playerResults");
  thePlayers.innerHTML = html;
}

function addPlayer() {
  addOrUpdate = "add";
  clearPlayerCrudPanel();
  showPlayerCrudPanel();
}

function updatePlayer() {
  addOrUpdate = "update";
  populatePlayerCrudPanel();
  showPlayerCrudPanel();
}

function showPlayerCrudPanel() {
  document
    .querySelector("#addUpdatePlayerContainer")
    .classList.remove("hidden");
}

function clearPlayerCrudPanel() {
  document.querySelector("#playerID").value = "";
  document.querySelector("#teamID").value = "";
  document.querySelector("#firstName").value = "";
  document.querySelector("#lastName").value = "";
  document.querySelector("#hometown").value = "";
  document.querySelector("#provinceCode").value = "";
}

function hidePlayerCrudPanel() {
  document.querySelector("#addUpdatePlayerContainer").classList.add("hidden");
}

function populatePlayerCrudPanel() {
  let selectedPlayer = document.querySelector(".selected");
  let teamID = Number(
    selectedPlayer.querySelector("td:nth-child(1)").innerHTML
  );
  let playerID = Number(
    selectedPlayer.querySelector("td:nth-child(2)").innerHTML
  );
  let firstName = selectedPlayer.querySelector("td:nth-child(3)").innerHTML;
  let lastName = selectedPlayer.querySelector("td:nth-child(4)").innerHTML;
  let hometown = selectedPlayer.querySelector("td:nth-child(5)").innerHTML;
  let provinceCode = selectedPlayer.querySelector("td:nth-child(6)").innerHTML;

  document.querySelector("#teamID").value = teamID;
  document.querySelector("#playerID").value = playerID;
  document.querySelector("#firstName").value = firstName;
  document.querySelector("#lastName").value = lastName;
  document.querySelector("#hometown").value = hometown;
  document.querySelector("#provinceCode").value = provinceCode;
}

function processPlayerCrud() {
  let playerID = Number(document.querySelector("#playerID").value);
  let teamID = Number(document.querySelector("#teamIDPlayer").value);
  let firstName = document.querySelector("#firstName").value;
  let lastName = document.querySelector("#lastName").value;
  let hometown = document.querySelector("#hometown").value;
  let provinceCode = document.querySelector("#provinceCode").value;

  console.log(playerID);
  console.log(teamID);
  let obj = {
    playerID: playerID,
    teamID: teamID,
    firstName: firstName,
    lastName: lastName,
    hometown: hometown,
    provinceCode: provinceCode,
  };

  let url = "playerService/players/" + playerID;
  let method = addOrUpdate === "add" ? "POST" : "PUT";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      console.log(resp);
      if (resp.data) {
        if (xhr.status === 200) {
          alert("Player updated.");
        } else if (xhr.status === 201) {
          alert("Player added.");
        }
      } else {
        alert(resp.error + " status code: " + xhr.status);
      }
      hidePlayerCrudPanel();
      getPlayers(teamID);
    }
  };
  xhr.open(method, url, true);
  xhr.send(JSON.stringify(obj));
}

function deletePlayer() {
  let row = document.querySelector(".selected");
  let id = Number(row.querySelectorAll("td")[0].innerHTML);
  let teamID = Number(row.querySelectorAll("td")[1].innerHTML);

  console.log(id);
  console.log(teamID);
  let url = "playerService/players/" + id;
  let method = "DELETE";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (resp.data) {
        alert("Player deleted");
      } else {
        alert(resp.error + " status code: " + xhr.status);
      }
      hidePlayerCrudPanel();
      getPlayers(teamID);
    }
  };
  xhr.open(method, url, true);
  xhr.send(id);
}

function handlePlayerClick(evt) {
  clearSelections();
  evt.target.parentElement.classList.add("selected");
  setDeleteUpdateButtonState(true);
}

function populatePlayerCrudPanel() {
  let selectedPlayer = document.querySelector(".selected");
  let playerID = Number(
    selectedPlayer.querySelector("td:nth-child(1)").innerHTML
  );
  let teamID = Number(
    selectedPlayer.querySelector("td:nth-child(2)").innerHTML
  );
  let firstName = selectedPlayer.querySelector("td:nth-child(3)").innerHTML;
  let lastName = selectedPlayer.querySelector("td:nth-child(4)").innerHTML;
  let hometown = selectedPlayer.querySelector("td:nth-child(5)").innerHTML;
  let provinceCode = selectedPlayer.querySelector("td:nth-child(6)").innerHTML;

  document.querySelector("#playerID").value = playerID;
  document.querySelector("#teamIDPlayer").value = teamID;
  document.querySelector("#firstName").value = firstName;
  document.querySelector("#lastName").value = lastName;
  document.querySelector("#hometown").value = hometown;
  document.querySelector("#provinceCode").value = provinceCode;
}

//all functions
function clearSelections() {
  let trs = document.querySelectorAll("tr");
  for (let i = 0; i < trs.length; i++) {
    trs[i].classList.remove("selected");
  }
}

function setDeleteUpdateButtonState(state) {
  if (state) {
    document.querySelector("#btnTeamDelete").removeAttribute("disabled");
    document.querySelector("#btnTeamUpdate").removeAttribute("disabled");
    document.querySelector("#btnPlayerDelete").removeAttribute("disabled");
    document.querySelector("#btnPlayerUpdate").removeAttribute("disabled");
  } else {
    document
      .querySelector("#btnTeamDelete")
      .setAttribute("disabled", "disabled");
    document
      .querySelector("#btnTeamUpdate")
      .setAttribute("disabled", "disabled");
    document
      .querySelector("#btnPlayerDelete")
      .setAttribute("disabled", "disabled");
    document
      .querySelector("#btnPlayerUpdate")
      .setAttribute("disabled", "disabled");
  }
}
