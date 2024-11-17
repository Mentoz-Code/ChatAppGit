// Select DOM elements
const searchBar = document.querySelector(".search input");
const searchIcon = document.querySelector(".search button");
const usersList = document.querySelector(".users-list");

// Toggle search bar visibility and clear it when the icon is clicked
searchIcon.onclick = () => {
    searchBar.classList.toggle("show");
    searchIcon.classList.toggle("active");
    searchBar.focus();

    if (searchBar.classList.contains("active")) {
        searchBar.value = "";
        searchBar.classList.remove("active");
    }
};

// Handle the search input keyup event
searchBar.onkeyup = () => {
    const searchTerm = searchBar.value.trim(); // Trim whitespace for cleaner input
    if (searchTerm !== "") {
        searchBar.classList.add("active");
    } else {
        searchBar.classList.remove("active");
    }

    // Send search request to the server
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/search.php", true);

    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            const data = xhr.response;
            usersList.innerHTML = data;
        }
    };

    // Set headers and send the request with the search term
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(`searchTerm=${encodeURIComponent(searchTerm)}`);
};
