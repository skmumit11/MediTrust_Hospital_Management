document.addEventListener("click", function (e) {
    const toggle = e.target.closest(".toggle-password");
    if (!toggle) return;

    const icon = toggle.querySelector("i");
    const targetId = toggle.getAttribute("data-target");
    const passwordInput = document.getElementById(targetId);

    if (!passwordInput) return;

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
});
