window.onload = function () {
  document
    .querySelector("#viewSchedule")
    .addEventListener("click", showSchedule);
  document.querySelector("#scoreGames").addEventListener("click", scoreGame);

  function showSchedule() {
    clearSelections();
    document.querySelector(".schedule-data").classList.add("visible");
    document.querySelector(".score-data").classList.remove("visible");
    getAllGames();
    document
      .querySelector("#scheduleTable")
      .addEventListener("click", handleRowClick);
  }
};
function scoreGame() {
  document.querySelector(".score-data").classList.add("visible");
  document.querySelector(".schedule-data").classList.remove("visible");
  let row = document.querySelector(".selected");
  let id = Number(row.querySelectorAll("td")[0].innerHTML);
  document.querySelector("#gameToScore").innerHTML = "Current Game: " + id;
  setScoreState(false);
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
          setScoreState(false);
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
  let available = false;
  for (let i = 0; i < arr.length; i++) {
    let row = arr[i];
    if (row.gameStateID !== "COMPLETE" || row.gameStateID !== "UNASSIGNED") {
      available = true;
      break;
    }
  }
  let html = "";
  if (available) {
    html =
      "<table><tr><th>Game ID<th>Match ID</th><th>Game Number</th><th>Game State</th></tr>";

    for (let i = 0; i < arr.length; i++) {
      let row = arr[i];
      if (row.gameStateID === "AVAILABLE") {
        html += "<tr>";
        html += "<td>" + row.gameID + "</td>";
        html += "<td>" + row.matchID + "</td>";
        html += "<td>" + row.gameNumber + "</td>";
        html += "<td>" + row.gameStateID + "</td>";
        html += "</tr>";
      }
    }
    html += "</table>";
  } else {
    let scores = rankQualifyingRound(arr);
    let ranking = bubbleSort(scores);

    for (let i = 0; i < scores.length; i++) {
      for (let j = 0; j < ranking.length; j++) {
        if (scores[i] === ranking[j]) {
          let obj = {
            matchID: i,
            round: "QUAL",
            matchgroup: 1,
            teamID: i,
            score: scores[i],
            ranking: j + 1,
          };
          let url = "matchUpService/matches/" + obj.matchID;
          let method = "PUT";
          let xhr = new XMLHttpRequest();
          xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
              let resp = JSON.parse(xhr.responseText);
              if (xhr.status === 200) {
                if (xhr.status === 200) {
                  console.log("it works");
                } else if (xhr.status === 500) {
                  alert("Server Error: " + resp.error);
                }
              }
            }
          };
          xhr.open(method, url, true);
          xhr.send(obj);
        }
      }
    }
  }
  let theTable = document.querySelector("#scheduleTable");
  theTable.innerHTML = html;
}
function handleRowClick(evt) {
  clearSelections();
  evt.target.parentElement.classList.add("selected");
  setScoreState(true);
}

function clearSelections() {
  let trs = document.querySelectorAll("tr");
  for (let i = 0; i < trs.length; i++) {
    trs[i].classList.remove("selected");
  }
}

function setScoreState(state) {
  if (state) {
    document.querySelector("#scoreGames").removeAttribute("disabled");
  } else
    document.querySelector("#scoreGames").setAttribute("disabled", "disabled");
}
function bubbleSort(arr) {
  for (let i = arr.length; i > 0; i--) {
    if (arr[i] > arr[i - 1]) {
      let temp = arr[i];
      arr[i] = arr[i - 1];
      arr[i - 1] = temp;
    }
  }
  return arr;
}
function rankQualifyingRound(arr) {
  let matchupScores = [];
  let gameTotal = 0;
  let matchID = -1;
  if (arr[399].gameStateID !== "AVAILABLE");
  for (let i = 0; i < arr.length; i++) {
    let game = arr[i];
    if (game.gameStateID === "COMPLETE") {
      console.log("does " + game.matchID + " equal " + matchID);
      if (matchID === -1) {
        matchID = game.matchID;
      }
      if (game.matchID !== matchID) {
        matchupScores.push(gameTotal);
        matchID = game.matchID;
        gameTotal = 0;
      }
      gameTotal += game.score;
    }
  }
  return matchupScores;
}
