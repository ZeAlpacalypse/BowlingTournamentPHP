window.onload = function () {
  document
    .querySelector("#viewSchedule")
    .addEventListener("click", showSchedule);
  document.querySelector("#scoreGames").addEventListener("click", showScore);
};
function showSchedule() {
  document.querySelector(".schedule-data").classList.add("visible");
  document.querySelector(".score-data").classList.remove("visible");
  getAllMatchUps();
}

function showScore() {
  document.querySelector(".schedule-data").classList.remove("visible");
  document.querySelector(".score-data").classList.add("visible");
}
function getAllMatchUps() {
  let url = "matchUpService/matches";
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
// text is a JSON string containing an array
function buildTable(text) {
  let arr = JSON.parse(text); // get JS Objects
  let html =
    "<table><tr><th>Match ID</th><th>Round</th><th>Match Group</th><th>Team ID</th><th>Score</th><th>Ranking</th></tr>";
  console.log(html);
  for (let i = 0; i < arr.length; i++) {
    let row = arr[i];
    html += "<tr>";
    html += "<td>" + row.matchID + "</td>";
    html += "<td>" + row.roundID + "</td>";
    html += "<td>" + row.matchGroup + "</td>";
    html += "<td>" + row.teamID + "</td>";
    html += "<td>" + row.score + "</td>";
    html += "<td>" + row.ranking + "</td>";
    html += "</tr>";
  }
  html += "</table>";
  console.log(html);
  let theTable = document.querySelector("#scheduleTable");
  theTable.innerHTML = html;
}
