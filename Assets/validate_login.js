function validateLoginForm(){
    var username = document.getElementsByName('username')[0].value;
    var password = document.getElementsByName('password')[0].value;

    if(username == ""){
        alert("Username is required!");
        return false;
    }

    if(password == ""){
        alert("Password is required!");
        return false;
    }

    return true;
}
