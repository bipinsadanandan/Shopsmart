document.getElementById('registerForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('register.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            if (data === "success") {
                alert("Registration successful!");
                window.location.href = "login.html";
            } else {
                alert("Error during registration. Please try again.");
            }
        });
});

document.getElementById('loginForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('login.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            if (data === "success") {
                alert("Login successful!");
                window.location.href = "welcome.php";
            } else if (data === "invalid") {
                alert("Invalid password!");
            } else {
                alert("User not found!");
            }
        });
});




document.addEventListener("DOMContentLoaded", () => {
    loadUserDetails();
    loadOrderDetails();
});

function loadUserDetails() {
    fetch('fetch_data.php?type=user')
        .then(response => response.text())
        .then(data => document.getElementById("user-details").innerHTML = data);
}

function loadOrderDetails() {
    fetch('fetch_data.php?type=orders')
        .then(response => response.text())
        .then(data => document.getElementById("order-details").innerHTML = data);
}

function searchOrders() {
    const searchQuery = document.getElementById("searchInput").value;
    fetch(`search_data.php?query=${searchQuery}`)
        .then(response => response.text())
        .then(data => document.getElementById("order-details").innerHTML = data);
}

function logout() {
    fetch('logout.php').then(() => window.location.href = 'login.html');
}
