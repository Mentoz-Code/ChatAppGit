// Select DOM elements
const searchBar = document.querySelector(".search input");
const searchIcon = document.querySelector(".search button");
const usersList = document.querySelector(".users-list");

// Toggle search bar visibility and clear input when the icon is clicked
searchIcon.onclick = () => {
    searchBar.classList.toggle("show");
    searchIcon.classList.toggle("active");
    searchBar.focus();

    if (searchBar.classList.contains("active")) {
        searchBar.value = "";
        searchBar.classList.remove("active");
    }
};

// Debounce timer for search input
let debounceTimer;

searchBar.onkeyup = () => {
    const searchTerm = searchBar.value.trim(); // Trim whitespace for cleaner input

    // Add or remove active class based on input
    if (searchTerm !== "") {
        searchBar.classList.add("active");
    } else {
        searchBar.classList.remove("active");
    }

    // Debounce search to prevent spamming requests
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        // Send search request to the server
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../ChatAppGit/php/search.php", true);

        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const data = xhr.response;
                    usersList.innerHTML = data;
                } else {
                    console.error("Failed to fetch search results.");
                }
            }
        };

        // Handle errors
        xhr.onerror = () => console.error("An error occurred during the XMLHttpRequest.");

        // Set headers and send the request with the search term
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(`searchTerm=${encodeURIComponent(searchTerm)}`);
    }, 1000); // Debounce delay
};

// Periodically update the users list if no search is active
setInterval(() => {
    if (!searchBar.classList.contains("active")) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "../ChatAppGit/php/users.php", true);

        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const data = xhr.response;
                    usersList.innerHTML = data;
                } else {
                    console.error("Failed to fetch users list.");
                }
            }
        };

        // Handle errors
        xhr.onerror = () => console.error("An error occurred during the XMLHttpRequest.");

        xhr.send();
    }
}, 1000); // Polling interval
