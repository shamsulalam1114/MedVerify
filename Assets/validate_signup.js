function validateSignupForm(){
    var full_name = document.getElementsByName('full_name')[0].value;
    var username = document.getElementsByName('username')[0].value;
    var password = document.getElementsByName('password')[0].value;
    var confirm_password = document.getElementsByName('confirm_password')[0].value;
    
    if(full_name == ""){
        alert("Full name is required!");
        return false;
    }
    
    if(username == ""){
        alert("Username is required!");
        return false;
    }
    
    if(password == ""){
        alert("Password is required!");
        return false;
    }
    
    if(confirm_password == ""){
        alert("Confirm password is required!");
        return false;
    }
    
    if(password != confirm_password){
        alert("Passwords do not match!");
        return false;
    }
    
    return true;
}
