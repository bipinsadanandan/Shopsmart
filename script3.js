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
