window.onload = function () {
  document
    .querySelector("#viewSchedule")
    .addEventListener("click", showSchedule);
  document.querySelector("#scoreGames").addEventListener("click", showScore);
};
function showSchedule() {
  document.querySelector(".schedule-data").classList.add("visible");
  document.querySelector(".score-data").classList.remove("visible");
}

function showScore() {
  document.querySelector(".schedule-data").classList.remove("visible");
  document.querySelector(".score-data").classList.add("visible");
}
