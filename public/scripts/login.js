
//change icon and input field to reveal password when clicked
function revealPassword(id) {
    let holder, input, icon
    holder = document.getElementById(id);
    input = holder.getElementsByTagName("input")[0];
    icon = holder.getElementsByTagName("i")[0];

    updateEye(icon);
    updateInput(input);
}

//change icon to have a slash through it, and the reverse
function updateEye(icon) {

    if(icon.classList.contains("fa-eye")) {
        icon.classList.replace("fa-eye", "fa-eye-slash");
    }
    else {
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

//change password input field into a standard text and inverse when clicked
function updateInput(input) {

    if(input.type == "password") {
        input.type = "text";
    }
    else {
        input.type = "password";
    }
}
