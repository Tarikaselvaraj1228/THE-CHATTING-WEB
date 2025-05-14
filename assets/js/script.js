// ===========================
// LOGIN FUNCTIONALITY
// ===========================
function loginUser() {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  fetch("login.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({
      username: username,
      password: password,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        alert("Login successful!");
        window.location.href = "chat.php";
      } else {
        alert("Login failed: " + data.message);
      }
    })
    .catch((err) => console.error("Login error:", err));
}

// ===========================
// CHAT FUNCTIONALITY
// ===========================
function loadMessages() {
  fetch("receiveMessages.php")
    .then((res) => res.json())
    .then((data) => {
      const chatBox = document.getElementById("chat-box");
      chatBox.innerHTML = "";

      data.messages.forEach((msg) => {
        const div = document.createElement("div");
        div.classList.add("message");
        div.innerHTML = `<strong>${msg.username}:</strong> ${msg.message}`;
        chatBox.appendChild(div);
      });

      chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch((err) => console.error("Load messages error:", err));
}

function sendMessage() {
  const messageInput = document.getElementById("message-input");
  const message = messageInput.value.trim();

  if (message === "") return;

  fetch("sendMessage.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ message: message }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        messageInput.value = "";
        loadMessages();
      } else {
        alert("Failed to send message.");
      }
    })
    .catch((err) => console.error("Send message error:", err));
}

// Auto-refresh messages
if (window.location.pathname.includes("chat.php")) {
  setInterval(loadMessages, 3000); // every 3 seconds
  document.addEventListener("DOMContentLoaded", loadMessages);
}

// ===========================
// FRIEND REQUEST FUNCTIONALITY
// ===========================
function getFriendRequests() {
  fetch("friendRequests.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ action: "get" }),
  })
    .then((res) => res.json())
    .then((data) => {
      const list = document.getElementById("friend-requests-list");
      list.innerHTML = "";

      if (data.requests.length === 0) {
        list.innerHTML = "<p>No pending friend requests.</p>";
        return;
      }

      data.requests.forEach((req) => {
        const item = document.createElement("div");
        item.innerHTML = `
          <p><strong>${req.username}</strong> sent you a friend request.</p>
          <button onclick="respondToRequest(${req.request_id}, 'accept')">Accept</button>
          <button onclick="respondToRequest(${req.request_id}, 'reject')">Reject</button>
        `;
        list.appendChild(item);
      });
    })
    .catch((err) => console.error("Get friend requests error:", err));
}

function respondToRequest(requestId, action) {
  fetch("friendRequests.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({
      action: action,
      request_id: requestId,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      alert(data.message);
      getFriendRequests();
    })
    .catch((err) => console.error("Respond to request error:", err));
}

function sendFriendRequest() {
  const receiverId = document.getElementById("receiver-id").value;

  fetch("friendRequests.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({
      action: "send",
      receiver_id: receiverId,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      alert(data.message);
    })
    .catch((err) => console.error("Send friend request error:", err));
}

// Auto-load friend requests on friendRequests.php
if (window.location.pathname.includes("friendRequests.php")) {
  document.addEventListener("DOMContentLoaded", getFriendRequests);
}


