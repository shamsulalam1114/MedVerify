
function loginAgain() {
    alert("Redirecting to login page...");
    window.location.href = "dashboard.html";
}


function goHome() {
    alert("Redirecting to homepage...");
    window.location.href = "dashboard.html";
}


document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("loginAgainBtn").addEventListener("click", loginAgain);
    document.getElementById("goHomeBtn").addEventListener("click", goHome);
});
