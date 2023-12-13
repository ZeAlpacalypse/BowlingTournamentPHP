window.onload = function () {
  document.querySelector("#loginButton").addEventListener("click", userLogin);
};
function userLogin() {
  let username = document.querySelector("#username").value;
  let password = document.querySelector("#password").value;

  if (username === "guest") {
    location.replace("guestDashboard.html");
  } else if (username === "scorekeeper") {
    location.replace("scorekeeperDashboard.html");
  } else if (username === "admin") {
    location.replace("adminDashboard.html");
  } else alert("Not logged in!");
}
