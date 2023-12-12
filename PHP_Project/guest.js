window.onload = function () {
  document.getElementById("viewTeamsBtn").addEventListener("click", showTeams);
  document
    .getElementById("viewStandingBtn")
    .addEventListener("click", showStandings);
};

function showTeams() {
  document.querySelector(".teams").classList.add("visible");
  document.querySelector(".standings").classList.remove("visible");
}

function showStandings() {
  document.querySelector(".teams").classList.remove("visible");
  document.querySelector(".standings").classList.add("visible");
}
