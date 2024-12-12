// Toggle password visibility
function togglePassword() {
    const passwordField = document.getElementById("password");
    const showPasswordText = document.querySelector(".show-password");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        showPasswordText.textContent = "Hide";
    } else {
        passwordField.type = "password";
        showPasswordText.textContent = "Show";
    }
}

// Validate password and redirect
function validatePassword() {
    const password = document.getElementById("password").value;
    const errorMessage = document.getElementById("error-message");

    if (password === "admin777") {
        window.location.href = "admin_dashboard.html";
    } else {
        errorMessage.textContent = "Incorrect password. Please try again.";
    }
}
