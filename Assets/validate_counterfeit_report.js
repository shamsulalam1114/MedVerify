function validateCounterfeitReport(){
    var barcode = document.getElementsByName('barcode')[0].value.trim();
    var batchNumber = document.getElementsByName('batch_number')[0].value.trim();
    var purchaseLocation = document.getElementsByName('purchase_location')[0].value.trim();
    var purchaseDate = document.getElementsByName('purchase_date')[0].value;
    var reportedIssue = document.getElementsByName('reported_issue')[0].value.trim();
    var evidencePhoto = document.getElementsByName('evidence_photo')[0].value;

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

    if(purchaseLocation == ""){
        alert("Purchase location is required!");
        return false;
    }

    if(purchaseDate == ""){
        alert("Purchase date is required!");
        return false;
    }

    // Check if purchase date is not in future
    var today = new Date();
    var purchase = new Date(purchaseDate);
    if(purchase > today){
        alert("Purchase date cannot be in the future!");
        return false;
    }

    if(reportedIssue == ""){
        alert("Please describe the issue you noticed!");
        return false;
    }

    if(reportedIssue.length < 20){
        alert("Please provide more details about the issue (at least 20 characters)!");
        return false;
    }

    if(evidencePhoto == ""){
        alert("Evidence photo is required!");
        return false;
    }

    // Validate file type
    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
    if(!allowedExtensions.exec(evidencePhoto)){
        alert("Please upload an image file (JPG, PNG, GIF)!");
        return false;
    }

    return true;
}
