// scripts.js

document.addEventListener("DOMContentLoaded", function() {
    // Scroll to the bottom of the chat when new messages are added
    const chatHistory = document.querySelector('.message-history');
    chatHistory.scrollTop = chatHistory.scrollHeight;
});
