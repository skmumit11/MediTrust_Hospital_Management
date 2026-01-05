document.addEventListener("DOMContentLoaded", function () {

    const verifyBtn = document.getElementById("verifyBtn");
    const verificationSection = document.getElementById("verification-section");
    const dynamicSection = document.getElementById("dynamic-section");
    const codeInput = document.getElementById("code");
    const emailInput = document.getElementById("email");
    const codeMessage = document.getElementById("codeMessage");
    const form = document.querySelector(".forgot-form");

    verifyBtn.addEventListener("click", function () {

        const enteredCode = codeInput.value.trim();
        const correctCode = "123"; // demo only

        clearMessage();

        if (enteredCode === "") {
            showMessage("Please enter the verification code.", "error");
            return;
        }



        showMessage("Verification successful! You can now reset your password.", "success");

        setTimeout(() => {
            verificationSection.style.display = "none";
            dynamicSection.innerHTML = "";

            // Add hidden email input so PHP receives it
            let hiddenEmail = document.createElement("input");
            hiddenEmail.type = "hidden";
            hiddenEmail.name = "email";
            hiddenEmail.value = emailInput.value;
            form.appendChild(hiddenEmail);

            // Add password fields with container for toggle
            createPasswordRow("NEW PASSWORD:", "password", "");
            createPasswordRow("CONFIRM PASSWORD:", "confirmpassword", "Re-enter password");

            // Add Reset & Resend buttons
            dynamicSection.insertAdjacentHTML("beforeend", `
                <tr>
                    <td>
                        <input type="submit" class="btn"
                               name="reset_password" value="Reset Password">
                    </td>
                </tr>
                <tr>
                    <td>
                        <button type="button"
                                id="resendBtn"
                                class="btn-secondary">
                            Resend Code
                        </button>
                    </td>
                </tr>
            `);

            // Attach Resend event
            document.getElementById("resendBtn")
                .addEventListener("click", resendCode);

            // Initialize toggle functionality for dynamically added fields
            initPasswordToggles();

        }, 300); // slightly faster
    });

    function resendCode() {
        dynamicSection.innerHTML = "";
        verificationSection.style.display = "block";

        codeInput.value = "";
        emailInput.value = "";

        // remove hidden email field
        const hiddenEmail = form.querySelector('input[name="email"][type="hidden"]');
        if (hiddenEmail) hiddenEmail.remove();

        clearMessage();
        showMessage("Verification code resent. Please check your email.", "success");
    }

    function showMessage(msg, type) {
        codeMessage.textContent = msg;
        codeMessage.className = type;
    }

    function clearMessage() {
        codeMessage.textContent = "";
        codeMessage.className = "";
    }

    function createPasswordRow(label, name, placeholder) {
        dynamicSection.insertAdjacentHTML("beforeend", `
            <tr>
                <td>${label}</td>
            </tr>
            <tr>
                <td class="password-field">
                    <input type="password" name="${name}" id="${name}" placeholder="${placeholder}" required>
                    <i class="fa-solid fa-eye toggle-password" data-target="${name}"></i>
                </td>
            </tr>
        `);
    }

    function initPasswordToggles() {
        const toggles = document.querySelectorAll(".toggle-password");
        toggles.forEach(toggle => {
            const targetID = toggle.dataset.target;
            const input = document.getElementById(targetID);

            if (input) {
                toggle.addEventListener("click", function () {
                    if (input.type === "password") {
                        input.type = "text";
                        toggle.classList.remove("fa-eye");
                        toggle.classList.add("fa-eye-slash");
                    } else {
                        input.type = "password";
                        toggle.classList.remove("fa-eye-slash");
                        toggle.classList.add("fa-eye");
                    }
                });
            }
        });
    }

});
