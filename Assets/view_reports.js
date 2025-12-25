document.addEventListener("DOMContentLoaded", function() {
    const addReportBtn = document.getElementById("addReportBtn");
    const clearFormBtn = document.getElementById("clearFormBtn");
    const reportDateInput = document.getElementById("reportDate");
    const reportTestInput = document.getElementById("reportTest");
    const reportDoctorInput = document.getElementById("reportDoctor");
    const reportStatusInput = document.getElementById("reportStatus");
    const reportsTable = document.getElementById("reportsTable");

    addReportBtn.addEventListener("click", function() {
        
        let reportDate = reportDateInput.value.trim();
        let reportTest = reportTestInput.value.trim();
        let reportDoctor = reportDoctorInput.value.trim();
        let reportStatus = reportStatusInput.value.trim();

        if (reportDate === "") {
            alert("Date field cannot be empty!");
            return;
        }

        if (reportTest === "") {
            alert("Test name field cannot be empty!");
            return;
        }

        if (reportDoctor === "") {
            alert("Doctor/Lab field cannot be empty!");
            return;
        }

        if (reportStatus === "") {
            alert("Status field cannot be empty!");
            return;
        }

        const newRow = document.createElement("tr");

        const dateCell = document.createElement("td");
        dateCell.innerHTML = reportDate;

        const testCell = document.createElement("td");
        testCell.innerHTML = reportTest;

        const doctorCell = document.createElement("td");
        doctorCell.innerHTML = reportDoctor;

        const statusCell = document.createElement("td");
        statusCell.innerHTML = reportStatus;

        const actionCell = document.createElement("td");
        const downloadBtn = document.createElement("button");
        downloadBtn.type = "button";
        downloadBtn.innerHTML = "Download";
        downloadBtn.onclick = function() {
            alert("Downloading Report");
        };
        actionCell.appendChild(downloadBtn);

        newRow.appendChild(dateCell);
        newRow.appendChild(testCell);
        newRow.appendChild(doctorCell);
        newRow.appendChild(statusCell);
        newRow.appendChild(actionCell);

        reportsTable.appendChild(newRow);

        alert("Report added successfully!");

        reportDateInput.value = "";
        reportTestInput.value = "";
        reportDoctorInput.value = "";
        reportStatusInput.value = "";
    });

    clearFormBtn.addEventListener("click", function() {
        reportDateInput.value = "";
        reportTestInput.value = "";
        reportDoctorInput.value = "";
        reportStatusInput.value = "";
    });
});

