<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AI Chatbot - Sistem PSM</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f8ff;
      margin: 0;
      padding: 0;
    }

    .chat-container {
      width: 400px;
      height: 600px;
      margin: 50px auto;
      background-color: #fff;
      border-radius: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .chat-box {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
    }

    .message {
      margin: 10px 0;
      padding: 10px 15px;
      border-radius: 15px;
      max-width: 80%;
    }

    .user {
      background-color: #007bff;
      color: white;
      align-self: flex-end;
    }

    .bot {
      background-color: #f1f0f0;
      color: #333;
      align-self: flex-start;
    }

    .input-area {
      display: flex;
      border-top: 1px solid #ccc;
    }

    input {
      flex: 1;
      padding: 15px;
      border: none;
      outline: none;
      font-size: 16px;
    }

    button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 15px 20px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <div class="chat-container">
    <div class="chat-box" id="chat-box">
      <div class="message bot">👋 Hai! Saya Chatbot PSM anda. Ada apa yang boleh saya bantu?</div>
    </div>
    <div class="input-area">
      <input type="text" id="user-input" placeholder="Tulis mesej anda...">
      <button onclick="sendMessage()">Hantar</button>
    </div>
  </div>

  <script>
    const chatBox = document.getElementById('chat-box');
    const userInput = document.getElementById('user-input');

    async function sendMessage() {
      const message = userInput.value.trim();
      if (!message) return;

      // Tambah mesej pengguna ke skrin
      chatBox.innerHTML += `<div class="message user">${message}</div>`;
      userInput.value = '';
      chatBox.scrollTop = chatBox.scrollHeight;

      // Panggil API Node.js backend
      try {
       const response = await fetch('http://localhost:5000/chat', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ message })
});


        const data = await response.json();
        const reply = data.reply || "Maaf, saya tak dapat menjawab sekarang.";

        // Papar balasan chatbot
        chatBox.innerHTML += `<div class="message bot">${reply}</div>`;
        chatBox.scrollTop = chatBox.scrollHeight;

      } catch (error) {
        chatBox.innerHTML += `<div class="message bot">⚠️ Ralat sambungan ke server.</div>`;
        console.error('Error:', error);
      }
    }
  </script>
</body>
</html>
