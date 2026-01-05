function validateEditAppointmentForm(){
    var appointmentDate = document.getElementsByName('appointment_date')[0].value;
    var doctorLab = document.getElementsByName('doctor_lab')[0].value;
    var appointmentType = document.getElementsByName('appointment_type')[0].value;

    if(appointmentDate == ""){
        alert("Appointment date is required!");
        return false;
    }

    if(doctorLab == ""){
        alert("Doctor/Lab name is required!");
        return false;
    }

    if(appointmentType == ""){
        alert("Appointment type is required!");
        return false;
    }

    return true;
}
