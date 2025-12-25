
let reportCounter = 7;


function addReportToTable(date, testName, doctor, status) {
    let table = document.getElementById("reportsTable");
    
    let newRow = "<tr>";
    newRow = newRow + "<td>" + date + "</td>";
    newRow = newRow + "<td>" + testName + "</td>";
    newRow = newRow + "<td>" + doctor + "</td>";
    newRow = newRow + "<td>" + status + "</td>";
    newRow = newRow + '<td><button type="button" onclick="alert(\'Downloading Report\')">Download</button></td>';
    newRow = newRow + "</tr>";
    
    table.innerHTML = table.innerHTML + newRow;
}


function addReport() {
    let dateInput = document.getElementById("reportDate");
    let testInput = document.getElementById("reportTest");
    let doctorInput = document.getElementById("reportDoctor");
    let statusInput = document.getElementById("reportStatus");
    
    let date = dateInput.value;
    let testName = testInput.value;
    let doctor = doctorInput.value;
    let status = statusInput.value;
    
    if (date === "" || testName === "" || doctor === "" || status === "") {
        alert("Please fill all fields!");
        return;
    }
    
    addReportToTable(date, testName, doctor, status);
    
    alert("Report added successfully!\nTest: " + testName + "\nStatus: " + status);
    
    clearForm();
}


function clearForm() {
    document.getElementById("reportDate").value = "";
    document.getElementById("reportTest").value = "";
    document.getElementById("reportDoctor").value = "";
    document.getElementById("reportStatus").value = "";
}


document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("addReportBtn").addEventListener("click", addReport);
    document.getElementById("clearFormBtn").addEventListener("click", clearForm);
});
