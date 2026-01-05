function validateReportForm(){
    var reportName = document.getElementsByName('report_name')[0].value;
    var reportType = document.getElementsByName('report_type')[0].value;
    var fileInput = document.getElementsByName('myfile')[0].value;

    if(reportName == ""){
        alert("Report name is required!");
        return false;
    }

    if(reportType == ""){
        alert("Report type is required!");
        return false;
    }

    if(fileInput == ""){
        alert("Please select a file to upload!");
        return false;
    }

    return true;
}
