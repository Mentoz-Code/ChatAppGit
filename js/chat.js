// Select DOM elements
const form = document.querySelector(".typing-area");
const incoming_id = form.querySelector(".incoming_id").value;
const inputField = form.querySelector(".input-field");
const sendBtn = form.querySelector("button");
const chatBox = document.querySelector(".chat-box");

// Prevent form submission
form.onsubmit = (e) => {
    e.preventDefault();
};

// Focus on the input field by default
inputField.focus();

// Enable or disable the send button based on input value
inputField.onkeyup = () => {
    sendBtn.classList.toggle("active", inputField.value.trim() !== "");
};

// Handle send button click event
sendBtn.onclick = () => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../ChatAppGit/php/insert-chat.php", true);

    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            inputField.value = ""; // Clear input field after successful message send
            scrollToBottom(); // Scroll to the bottom of the chat box
        }
    };

    // Send form data
    const formData = new FormData(form);
    xhr.send(formData);
};

// Add active class when the mouse enters the chat box
chatBox.onmouseenter = () => {
    chatBox.classList.add("active");
};

// Remove active class when the mouse leaves the chat box
chatBox.onmouseleave = () => {
    chatBox.classList.remove("active");
};

setInterval(() => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../ChatAppGit/php/get-chat.php", true);

    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            const data = xhr.response;

            // Update chatBox content and scroll if not active
            chatBox.innerHTML = data;

            if (!chatBox.classList.contains("active")) {
                scrollToBottom(); // Keep chat scrolled to the bottom if not focused
            }
        }
    };

    // Send the request with the current incoming user ID
    const formData = new FormData();
    formData.append("incoming_id", incoming_id); // Use the existing incoming_id variable
    xhr.send(formData);
}, 500);

// Scroll to the bottom of the chat box
function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
}
