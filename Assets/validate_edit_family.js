function validateEditFamilyForm(){
    var name = document.getElementsByName('name')[0].value;
    var relationship = document.getElementsByName('relationship')[0].value;
    var age = document.getElementsByName('age')[0].value;

    if(name == ""){
        alert("Name is required!");
        return false;
    }

    if(relationship == ""){
        alert("Relationship is required!");
        return false;
    }

    if(age == ""){
        alert("Age is required!");
        return false;
    }

    if(age < 0 || age > 150){
        alert("Age must be between 0 and 150!");
        return false;
    }

    return true;
}
