// assets/signupValidate.js

function validateSignupForm() {
    const form = document.forms["signupForm"];
    let valid = true;

    // Clear previous errors
    const errorSpans = document.querySelectorAll(".error-msg");
    errorSpans.forEach(span => span.innerText = "");

    // Full Name
    const fullname = form["fullname"].value.trim();
    if (fullname === "") {
        document.getElementById("fullnameError").innerText = "Full name is required";
        valid = false;
    }

    // Username
    const username = form["username"].value.trim();
    if (username === "") {
        document.getElementById("usernameError").innerText = "Username is required";
        valid = false;
    } else if (username.length < 4) {
        document.getElementById("usernameError").innerText = "Username must be at least 4 characters";
        valid = false;
    }

    // Email
    const email = form["email"].value.trim();
    if (email === "") {
        document.getElementById("emailError").innerText = "Email is required";
        valid = false;
    } else if (email.indexOf("@") === -1 || email.indexOf(".") === -1 || email.indexOf("@") > email.lastIndexOf(".")) {
        document.getElementById("emailError").innerText = "Enter a valid email";
        valid = false;
    }

    // DOB
    const dob = form["dob"].value;
    if (dob === "") {
        document.getElementById("dobError").innerText = "Date of birth is required";
        valid = false;
    }

    // Gender
    const genderMale = form["gender"][0].checked;
    const genderFemale = form["gender"][1].checked;
    if (!genderMale && !genderFemale) {
        document.getElementById("genderError").innerText = "Gender is required";
        valid = false;
    }

    // Address
    const address = form["address"].value.trim();
    if (address === "") {
        document.getElementById("addressError").innerText = "Address is required";
        valid = false;
    }

    // Password
    const password = form["password"].value;
    if (password === "") {
        document.getElementById("passwordError").innerText = "Password is required";
        valid = false;
    } else if (password.length < 6) {
        document.getElementById("passwordError").innerText = "Password must be at least 6 characters";
        valid = false;
    }

    // Confirm Password
    const confirmpassword = form["confirmpassword"].value;
    if (confirmpassword === "") {
        document.getElementById("confirmpasswordError").innerText = "Confirm your password";
        valid = false;
    } else if (password !== confirmpassword) {
        document.getElementById("confirmpasswordError").innerText = "Passwords do not match";
        valid = false;
    }

    return valid; // Submit only if valid
}
