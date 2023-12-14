window.onload = function () {
  document
    .querySelector("#loginButton")
    .addEventListener("click", getUserByUsername);
};
function userLogin(text) {
  let user = JSON.parse(text);
  console.log(user);

  /*if (username === "guest") {
    location.replace("guestDashboard.html");
  } else if (username === "scorekeeper") {
    location.replace("scorekeeperDashboard.html");
  } else if (username === "admin") {
    location.replace("adminDashboard.html");
  } else alert("Not logged in!");*/
}
function getUserByUsername() {
  let username = document.querySelector("#username").value;
  let url = "userService/users/";
  url += username;
  console.log(url);
  let method = "GET";
  let xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      let resp = JSON.parse(xhr.responseText);
      if (xhr.status === 200) {
        if (resp.data) {
          userLogin(resp.data);
        } else if (xhr.status === 500) {
          alert("Server Error: " + resp.error);
        }
      }
    }
  };
  xhr.open(method, url, true);
  xhr.send();
}
