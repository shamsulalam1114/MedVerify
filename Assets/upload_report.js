let reportName = "";
let reportDate = "";
let reportType = "";
let reportFile = "";


function addReportToTable(name, date, type) {
    let table = document.getElementById("uploadedReportsTable");
    
    let newRow = "<tr>";
    newRow = newRow + "<td>" + name + "</td>";
    newRow = newRow + "<td>" + date + "</td>";
    newRow = newRow + "<td>" + type + "</td>";
    newRow = newRow + "</tr>";
    
    table.innerHTML = table.innerHTML + newRow;
}


function uploadReport() {
    let nameInput = document.getElementById("reportName");
    let dateInput = document.getElementById("reportDate");
    let typeInput = document.getElementById("reportType");
    let fileInput = document.getElementById("reportFile");
    
    reportName = nameInput.value;
    reportDate = dateInput.value;
    reportType = typeInput.value;
    reportFile = fileInput.value;
    
    if (reportName === "" || reportDate === "" || reportType === "" || reportFile === "") {
        alert("Please fill all fields!");
        return;
    }
    
    addReportToTable(reportName, reportDate, reportType);
    
    alert("Report uploaded successfully!");
    
    clearForm();
}


function clearForm() {
    document.getElementById("reportName").value = "";
    document.getElementById("reportDate").value = "";
    document.getElementById("reportType").value = "";
    document.getElementById("reportFile").value = "";
}


document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("uploadBtn").addEventListener("click", uploadReport);
    document.getElementById("clearBtn").addEventListener("click", clearForm);
});

