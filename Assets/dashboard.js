document.addEventListener("DOMContentLoaded", function() {
    const addVerificationBtn = document.getElementById("addVerificationBtn");
    const addReportBtn = document.getElementById("addReportBtn");
    const resetBtn = document.getElementById("resetBtn");
    const verificationsCount = document.getElementById("verificationsCount");
    const appointmentsCount = document.getElementById("appointmentsCount");
    const reportsCount = document.getElementById("reportsCount");

    let totalVerifications = 0;
    let upcomingAppointment = "Oct 24";
    let totalReports = 5;

    verificationsCount.innerHTML = totalVerifications;
    appointmentsCount.innerHTML = upcomingAppointment;
    reportsCount.innerHTML = totalReports;

    addVerificationBtn.addEventListener("click", function() {
        totalVerifications = totalVerifications + 1;
        verificationsCount.innerHTML = totalVerifications;
    });

    addReportBtn.addEventListener("click", function() {
        totalReports = totalReports + 1;
        reportsCount.innerHTML = totalReports;
    });

    resetBtn.addEventListener("click", function() {
        totalVerifications = 0;
        upcomingAppointment = "Oct 24";
        totalReports = 5;
        verificationsCount.innerHTML = totalVerifications;
        appointmentsCount.innerHTML = upcomingAppointment;
        reportsCount.innerHTML = totalReports;
    });
});

