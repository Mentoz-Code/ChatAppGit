// Select the login form, the continue button, and the error text container
const form = document.querySelector(".login form"),
    continueBtn = form.querySelector(".button input"),
    errorText = form.querySelector(".error-text");

// Prevent the default form submission behavior (e.g., page reload)
form.onsubmit = (e) => {
    e.preventDefault(); // Stops traditional form submission
};

// Add a click event listener to the "continue" button
continueBtn.onclick = () => {
    // Create a new XMLHttpRequest object for sending the login request
    let xhr = new XMLHttpRequest();

    // Initialize a POST request to the login PHP script
    xhr.open("POST", "../ChatAppGit/php/login.php", true);

    // Define what happens when the response is received
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) { // Ensure the request is complete
            if (xhr.status === 200) { // Check for a successful HTTP response
                let data = xhr.response.trim(); // Get the response data and remove excess whitespace

                // Check the response for success or error
                if (data === "success") {
                    location.href = "../ChatAppGit/users.php"; // Redirect to the users page on success
                } else {
                    // Display the error message returned from the server
                    errorText.style.display = "block"; // Make the error text visible
                    errorText.textContent = data; // Set the error message
                }
            } else {
                // Handle unexpected HTTP status codes (non-200)
                errorText.style.display = "block";
                errorText.textContent = "Error: Couldn't communicate with the server.";
            }
        }
    };

    // Handle network errors (e.g., server unreachable)
    xhr.onerror = () => {
        errorText.style.display = "block";
        errorText.textContent = "Network error. Please try again later.";
    };

    // Create a FormData object to collect the form's input values
    let formData = new FormData(form);

    // Send the form data to the server
    xhr.send(formData);
};
