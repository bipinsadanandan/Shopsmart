// Handle form submission for Contact Us page
document.getElementById('contact-form').addEventListener('submit', function (e) {
    e.preventDefault();

    // Get form values
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;

    // Perform validation (basic)
    if (name === '' || email === '' || message === '') {
        alert("Please fill out all fields.");
        return;
    }

    // Send form data via AJAX (simulating AJAX request here)
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'contact_form_submission_url', true); // Use an actual URL here
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Show success message on form submission
            document.getElementById('response-message').innerText = "Thank you for reaching out. We will get back to you soon!";
            document.getElementById('contact-form').reset();
        }
    };

    // Create request data
    const formData = JSON.stringify({ name, email, message });
    xhr.send(formData);
});
