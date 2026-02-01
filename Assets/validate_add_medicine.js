function validateAddMedicineForm(){
    var medicineName = document.getElementsByName('medicine_name')[0].value.trim();
    var manufacturerId = document.getElementsByName('manufacturer_id')[0].value;
    var category = document.getElementsByName('category')[0].value;
    var dosageForm = document.getElementsByName('dosage_form')[0].value;
    var strength = document.getElementsByName('strength')[0].value.trim();
    var barcode = document.getElementsByName('barcode')[0].value.trim();
    var batchNumber = document.getElementsByName('batch_number')[0].value.trim();
    var manufacturingDate = document.getElementsByName('manufacturing_date')[0].value;
    var expiryDate = document.getElementsByName('expiry_date')[0].value;
    var mrp = document.getElementsByName('mrp')[0].value;

    if(medicineName == ""){
        alert("Medicine name is required!");
        return false;
    }

    if(manufacturerId == ""){
        alert("Please select a manufacturer!");
        return false;
    }

    if(category == ""){
        alert("Please select a category!");
        return false;
    }

    if(dosageForm == ""){
        alert("Please select dosage form!");
        return false;
    }

    if(strength == ""){
        alert("Strength is required!");
        return false;
    }

    if(barcode == ""){
        alert("Barcode is required!");
        return false;
    }

    // Validate barcode format (10-13 digits)
    if(!/^\d{10,13}$/.test(barcode)){
        alert("Barcode must be 10-13 digits!");
        return false;
    }

    if(batchNumber == ""){
        alert("Batch number is required!");
        return false;
    }

    if(manufacturingDate == ""){
        alert("Manufacturing date is required!");
        return false;
    }

    if(expiryDate == ""){
        alert("Expiry date is required!");
        return false;
    }

    // Validate expiry date is after manufacturing date
    if(new Date(expiryDate) <= new Date(manufacturingDate)){
        alert("Expiry date must be after manufacturing date!");
        return false;
    }

    if(mrp == "" || mrp <= 0){
        alert("Please enter a valid MRP!");
        return false;
    }

    return true;
}
