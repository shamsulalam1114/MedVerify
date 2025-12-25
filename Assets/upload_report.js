document.addEventListener("DOMContentLoaded", function() {
    const uploadBtn = document.getElementById("uploadBtn");
    const clearBtn = document.getElementById("clearBtn");
    const reportNameInput = document.getElementById("reportName");
    const reportDateInput = document.getElementById("reportDate");
    const reportTypeInput = document.getElementById("reportType");
    const reportFileInput = document.getElementById("reportFile");
    const uploadedReportsTable = document.getElementById("uploadedReportsTable");

    uploadBtn.addEventListener("click", function() {
        
        let reportName = reportNameInput.value.trim();
        let reportDate = reportDateInput.value.trim();
        let reportType = reportTypeInput.value.trim();
        let reportFile = reportFileInput.value.trim();

        if (reportName === "") {
            alert("Report name field cannot be empty!");
            return;
        }

        if (reportDate === "") {
            alert("Report date field cannot be empty!");
            return;
        }

        if (reportType === "") {
            alert("Report type field cannot be empty!");
            return;
        }

        if (reportFile === "") {
            alert("File field cannot be empty!");
            return;
        }

        const newRow = document.createElement("tr");

        const nameCell = document.createElement("td");
        nameCell.innerHTML = reportName;

        const dateCell = document.createElement("td");
        dateCell.innerHTML = reportDate;

        const typeCell = document.createElement("td");
        typeCell.innerHTML = reportType;

        newRow.appendChild(nameCell);
        newRow.appendChild(dateCell);
        newRow.appendChild(typeCell);

        uploadedReportsTable.appendChild(newRow);

        alert("Report uploaded successfully!");

        reportNameInput.value = "";
        reportDateInput.value = "";
        reportTypeInput.value = "";
        reportFileInput.value = "";
    });

    clearBtn.addEventListener("click", function() {
        reportNameInput.value = "";
        reportDateInput.value = "";
        reportTypeInput.value = "";
        reportFileInput.value = "";
    });
});


