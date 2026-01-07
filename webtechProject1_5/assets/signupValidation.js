
function validateSignupForm() {
    const form = document.forms["signupForm"];
    let valid = true;

    const errorSpans = document.querySelectorAll(".error-msg");
    errorSpans.forEach(span => span.innerText = "");

    const fullname = form["fullname"].value.trim();
    if (fullname === "") {
        const el = document.getElementById("fullnameError");
        if (el) el.innerText = "Full name is required";
        valid = false;
    }

    const username = form["username"].value.trim();
    if (username === "") {
        const el = document.getElementById("usernameError");
        if (el) el.innerText = "Username is required";
        valid = false;
    } else if (username.length < 4) {
        const el = document.getElementById("usernameError");
        if (el) el.innerText = "Username must be at least 4 characters";
        valid = false;
    }

    const email = form["email"].value.trim();
    if (email === "") {
        const el = document.getElementById("emailError");
        if (el) el.innerText = "Email is required";
        valid = false;
    } else {
        const at = email.indexOf("@");
        const dot = email.lastIndexOf(".");
        if (at === -1 || dot === -1 || at > dot) {
            const el = document.getElementById("emailError");
            if (el) el.innerText = "Enter a valid email";
            valid = false;
        }
    }

    const dob = form["dob"].value;
    if (dob === "") {
        const el = document.getElementById("dobError");
        if (el) el.innerText = "Date of birth is required";
        valid = false;
    }

    let genderSelected = false;

    if (form["gender"]) {
        const g = form["gender"];

        if (g.length !== undefined) {
            for (let i = 0; i < g.length; i++) {
                if (g[i].checked) {
                    genderSelected = true;
                    break;
                }
            }
        } else {
            if (g.value && g.value.trim() !== "") genderSelected = true;
        }
    }

    if (!genderSelected) {
        const el = document.getElementById("genderError");
        if (el) el.innerText = "Gender is required";
        valid = false;
    }

    const address = form["address"].value.trim();
    if (address === "") {
        const el = document.getElementById("addressError");
        if (el) el.innerText = "Address is required";
        valid = false;
    }

    const password = form["password"].value;
    if (password === "") {
        const el = document.getElementById("passwordError");
        if (el) el.innerText = "Password is required";
        valid = false;
    } else if (password.length < 6) {
        const el = document.getElementById("passwordError");
        if (el) el.innerText = "Password must be at least 6 characters";
        valid = false;
    }

    const confirmpassword = form["confirmpassword"].value;
    if (confirmpassword === "") {
        const el = document.getElementById("confirmpasswordError");
        if (el) el.innerText = "Confirm your password";
        valid = false;
    } else if (password !== confirmpassword) {
        const el = document.getElementById("confirmpasswordError");
        if (el) el.innerText = "Passwords do not match";
        valid = false;
    }

    return valid;
}

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("signupForm");
    if (form) {
        form.addEventListener("submit", function (e) {
            if (!validateSignupForm()) e.preventDefault();
        });
    }
});
