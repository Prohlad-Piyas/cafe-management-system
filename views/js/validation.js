

function showError(spanId, message) {
    document.getElementById(spanId).textContent = message;
}

function clearErrors(spanIds) {
    spanIds.forEach(function (id) {
        document.getElementById(id).textContent = "";
    });
}

function valueOf(inputId) {
    return document.getElementById(inputId).value.trim();
}




function validateLogin() {
    clearErrors(["emailErr", "passErr"]);

    const email = valueOf("email");
    const pass = document.getElementById("pass").value;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;

    let hasErr = false;

    if (email === "") {
        showError("emailErr", "Enter your email address");
        hasErr = true;
    } else if (!emailPattern.test(email)) {
        showError("emailErr", "That does not look like an email address");
        hasErr = true;
    }

    if (pass === "") {
        showError("passErr", "Enter your password");
        hasErr = true;
    }

    return !hasErr;
}




function validateRegister() {
    clearErrors(["nameErr", "phoneErr", "emailErr", "passErr", "conPassErr"]);

    const name = valueOf("name");
    const phone = valueOf("phone");
    const email = valueOf("email");
    const pass = document.getElementById("pass").value;
    const conPass = document.getElementById("conPass").value;
    const namePattern = /^[A-Za-z. ]{3,}$/;
    const phonePattern = /^01[3-9][0-9]{8}$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
    const passPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

    let hasErr = false;
    if (name === "") {
        showError("nameErr", "Enter your full name");
        hasErr = true;
    } else if (name.length < 3) {
        showError("nameErr", "Name must be at least 3 characters");
        hasErr = true;
    } else if (!namePattern.test(name)) {
        showError("nameErr", "Name can only contain letters, spaces and dots");
        hasErr = true;
    }

    if (phone === "") {
        showError("phoneErr", "Enter your phone number");
        hasErr = true;
    } else if (!phonePattern.test(phone)) {
        showError("phoneErr", "Use an 11 digit number starting with 01");
        hasErr = true;
    }

    if (email === "") {
        showError("emailErr", "Enter your email address");
        hasErr = true;
    } else if (!emailPattern.test(email)) {
        showError("emailErr", "That does not look like an email address");
        hasErr = true;
    }

    if (pass === "") {
        showError("passErr", "Enter a password");
        hasErr = true;
    } else if (!passPattern.test(pass)) {
        showError("passErr", "At least 8 characters with one capital, one small letter and one number");
        hasErr = true;
    }

    if (conPass === "") {
        showError("conPassErr", "Type the password again");
        hasErr = true;
    } else if (conPass !== pass) {
        showError("conPassErr", "Both passwords must match");
        hasErr = true;
    }

    return !hasErr;
}




function validateForgot() {
    clearErrors(["emailErr"]);

    const email = valueOf("email");
    const emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;

    if (email === "") {
        showError("emailErr", "Enter your email address");
        return false;
    }

    if (!emailPattern.test(email)) {
        showError("emailErr", "That does not look like an email address");
        return false;
    }

    return true;
}




const PASS_PATTERN = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
const PASS_MESSAGE =
    "At least 8 characters with one capital, one small letter and one number";

function checkNewPassword(passId, conId, passErrId, conErrId) {
    const pass = document.getElementById(passId).value;
    const conPass = document.getElementById(conId).value;

    let hasErr = false;

    if (pass === "") {
        showError(passErrId, "Enter a password");
        hasErr = true;
    } else if (!PASS_PATTERN.test(pass)) {
        showError(passErrId, PASS_MESSAGE);
        hasErr = true;
    }

    if (conPass === "") {
        showError(conErrId, "Type the password again");
        hasErr = true;
    } else if (conPass !== pass) {
        showError(conErrId, "Both passwords must match");
        hasErr = true;
    }

    return !hasErr;
}




function validateChangePassword() {
    clearErrors(["currentPassErr", "newPassErr", "conPassErr"]);

    const current = document.getElementById("currentPass").value;
    const newPass = document.getElementById("newPass").value;

    let ok = checkNewPassword("newPass", "conPass", "newPassErr", "conPassErr");

    if (current === "") {
        showError("currentPassErr", "Enter your current password");
        ok = false;
    } else if (current === newPass && newPass !== "") {
        showError("newPassErr", "New password must be different from the current one");
        ok = false;
    }

    return ok;
}




function validateReset() {
    clearErrors(["newPassErr", "conPassErr"]);
    return checkNewPassword("newPass", "conPass", "newPassErr", "conPassErr");
}
