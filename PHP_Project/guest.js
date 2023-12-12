window.onload = function () {
  document.getElementById("viewTeamsBtn").addEventListener("click", showTeams);
  document
    .getElementById("viewStandingBtn")
    .addEventListener("click", showStandings);
};

function showTeams() {
  document.querySelector(".teams").classList.add("visible");
  document.querySelector(".standings").classList.remove("visible");
  getAllTeams();
}

function showStandings() {
  document.querySelector(".teams").classList.remove("visible");
  document.querySelector(".standings").classList.add("visible");
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
    html += "<td><p>View Team</p><p>Edit Team</p></td>";
    html += "</tr>";
  }
  html += "</table>";
  let theTable = document.querySelector("#teamsTable");
  theTable.innerHTML = html;
}
