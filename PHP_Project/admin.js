window.onload = function () {
  document
    .querySelector("#editTeams")
    .addEventListener("click", showTeamEditor);
  document
    .querySelector("#setupRound")
    .addEventListener("click", showRoundSetup);
};

function showTeamEditor() {
  document.querySelector(".team-editor").classList.add("visible");
  document.querySelector(".round-setup").classList.remove("visible");
}

function showRoundSetup() {
  document.querySelector(".team-editor").classList.remove("visible");
  document.querySelector(".round-setup").classList.add("visible");
}
