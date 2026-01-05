function validateEditReportForm(){
    var reportName = document.getElementsByName('report_name')[0].value;
    var reportType = document.getElementsByName('report_type')[0].value;
    var filePath = document.getElementsByName('file_path')[0].value;

    if(reportName == ""){
        alert("Report name is required!");
        return false;
    }

    if(reportType == ""){
        alert("Report type is required!");
        return false;
    }

    if(filePath == ""){
        alert("File path is required!");
        return false;
    }

    return true;
}
