// Calendar Page JavaScript

// Function to add appointment to today's table
function addAppointmentToTable(date, time, doctor, type) {
    let table = document.getElementById("todayAppointmentsTable");
    
    let newRow = "<tr>";
    newRow = newRow + "<td>" + time + "</td>";
    newRow = newRow + "<td>" + doctor + "</td>";
    newRow = newRow + "<td>" + type + "</td>";
    newRow = newRow + "<td>Scheduled</td>";
    newRow = newRow + '<td><button type="button" onclick="alert(\'Viewing appointment details\')">View</button></td>';
    newRow = newRow + "</tr>";
    
    table.innerHTML = table.innerHTML + newRow;
}

// Function to schedule appointment
function scheduleAppointment() {
    let dateInput = document.getElementById("appointmentDate");
    let timeInput = document.getElementById("appointmentTime");
    let doctorInput = document.getElementById("appointmentDoctor");
    let typeInput = document.getElementById("appointmentType");
    
    let date = dateInput.value;
    let time = timeInput.value;
    let doctor = doctorInput.value;
    let type = typeInput.value;
    
    if (date === "" || time === "" || doctor === "" || type === "") {
        alert("Please fill all fields!");
        return;
    }
    
    addAppointmentToTable(date, time, doctor, type);
    
    alert("Appointment scheduled successfully!\nDoctor: " + doctor + "\nDate: " + date + "\nTime: " + time);
    
    clearForm();
}

// Function to clear form
function clearForm() {
    document.getElementById("appointmentDate").value = "";
    document.getElementById("appointmentTime").value = "";
    document.getElementById("appointmentDoctor").value = "";
    document.getElementById("appointmentType").value = "";
}

// Wait for page to load
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("addAppointmentBtn").addEventListener("click", scheduleAppointment);
    document.getElementById("clearFormBtn").addEventListener("click", clearForm);
});
