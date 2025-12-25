document.addEventListener("DOMContentLoaded", function() {
    const loginAgainBtn = document.getElementById("loginAgainBtn");
    const goHomeBtn = document.getElementById("goHomeBtn");

    loginAgainBtn.addEventListener("click", function() {
        alert("Redirecting to login page...");
        window.location.href = "login.php";
    });

    goHomeBtn.addEventListener("click", function() {
        alert("Redirecting to homepage...");
        window.location.href = "dashboard.php";
    });
});

