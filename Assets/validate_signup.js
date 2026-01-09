function validateSignupForm(){
    var full_name = document.getElementsByName('full_name')[0].value;
    var username = document.getElementsByName('username')[0].value;
    var password = document.getElementsByName('password')[0].value;
    var confirm_password = document.getElementsByName('confirm_password')[0].value;
    
    // Full name validation
    if(full_name == ""){
        alert("Full name is required!");
        return false;
    }
    
    if(full_name.length < 3){
        alert("Full name must be at least 3 characters!");
        return false;
    }
    
    if(full_name.length > 50){
        alert("Full name must be less than 50 characters!");
        return false;
    }
    
    // Check if full name contains only letters and spaces
    var hasInvalidChar = false;
    for(var i = 0; i < full_name.length; i++){
        var char = full_name[i];
        if(!((char >= 'a' && char <= 'z') || (char >= 'A' && char <= 'Z') || char == ' ')){
            hasInvalidChar = true;
            break;
        }
    }
    
    if(hasInvalidChar){
        alert("Full name should only contain letters and spaces!");
        return false;
    }
    
    // Username validation
    if(username == ""){
        alert("Username is required!");
        return false;
    }
    
    if(username.length < 4){
        alert("Username must be at least 4 characters!");
        return false;
    }
    
    if(username.length > 20){
        alert("Username must be less than 20 characters!");
        return false;
    }
    
    // Check if username contains spaces
    if(username.indexOf(' ') >= 0){
        alert("Username cannot contain spaces!");
        return false;
    }
    
    // Password validation
    if(password == ""){
        alert("Password is required!");
        return false;
    }
    
    if(password.length < 6){
        alert("Password must be at least 6 characters!");
        return false;
    }
    
    if(password.length > 30){
        alert("Password must be less than 30 characters!");
        return false;
    }
    
    // Confirm password validation
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
