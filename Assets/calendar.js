
function addAppointment() {
    let dateInput = document.getElementById("appointmentDate");
    let doctorInput = document.getElementById("appointmentDoctor");
    let typeInput = document.getElementById("appointmentType");
    
    let date = dateInput.value;
    let doctor = doctorInput.value;
    let type = typeInput.value;
    
    if (date === "" || doctor === "" || type === "") {
        alert("Please fill all fields!");
        return;
    }
    
    let table = document.getElementById("appointmentsTable");
    
    let newRow = "<tr>";
    newRow = newRow + "<td>" + date + "</td>";
    newRow = newRow + "<td>" + doctor + "</td>";
    newRow = newRow + "<td>" + type + "</td>";
    newRow = newRow + "</tr>";
    
    table.innerHTML = table.innerHTML + newRow;
    
    alert("Appointment added successfully!");
    
    clearForm();
}


function clearForm() {
    document.getElementById("appointmentDate").value = "";
    document.getElementById("appointmentDoctor").value = "";
    document.getElementById("appointmentType").value = "";
}


document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("addAppointmentBtn").addEventListener("click", addAppointment);
    document.getElementById("clearFormBtn").addEventListener("click", clearForm);
});
