function validateMedicineForm(){
    var barcode = document.getElementsByName('barcode_scanned')[0].value.trim();
    var batchNumber = document.getElementsByName('batch_number_entered')[0].value.trim();

    if(barcode == "" && batchNumber == ""){
        alert("Please enter either Barcode or Batch Number!");
        return false;
    }

    // Basic barcode validation (should be numeric and 10-13 digits)
    if(barcode != "" && !/^\d{10,13}$/.test(barcode)){
        alert("Invalid barcode format! Barcode should be 10-13 digits.");
        return false;
    }

    return true;
}
