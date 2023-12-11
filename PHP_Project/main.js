window.onload = function () {
  document.querySelector("#loginButton").addEventListener("click", userLogin);
};
function userLogin() {
  alert("logged in");
  location.replace("guestDashboard.html");
}
