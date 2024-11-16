// Select the signup form, continue button, and error text elements
const form = document.querySelector(".signup form"),
    continueBtn = form.querySelector(".button input"),
    errorText = form.querySelector(".error-text");

// Prevent the default form submission behavior
form.onsubmit = (e) => {
    e.preventDefault(); // Stop traditional form submission
};

// Add a click event listener to the continue button
continueBtn.onclick = () => {
    // Create a new XMLHttpRequest object
    let xhr = new XMLHttpRequest();

    // Initialize a POST request to the signup PHP script
    xhr.open("POST", "../php/signup.php", true);

    // Define what happens when the request is loaded
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) { // Ensure request is complete
            if (xhr.status === 200) { // Check for successful response
                let data = xhr.response.trim(); // Get and trim response data
                if (data === "Success") {
                    location.href = "../users.php"; // Redirect to the users page
                } else {
                    errorText.style.display = "block"; // Show error text
                    errorText.textContent = data; // Set error message
                }
            } else {
                // Handle non-200 status codes
                errorText.style.display = "block";
                errorText.textContent = "Error: Failed to communicate with the server.";
            }
        }
    };

    // Handle potential network errors
    xhr.onerror = () => {
        errorText.style.display = "block";
        errorText.textContent = "Error: Network issue. Please try again later.";
    };

    // Create a FormData object from the form
    let formData = new FormData(form);

    // Send the form data to the server
    xhr.send(formData);
};
