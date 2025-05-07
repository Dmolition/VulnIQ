<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Phishing Detection Game</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #121212;
      color: #f0f0f0;
      margin: 0;
      padding: 20px;
      text-align: center;
    }

    h1 {
      color: #ffffff;
      margin-bottom: 10px;
    }

    .instructions {
      margin-bottom: 20px;
      color: #cccccc;
    }

    .game-container {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
    }

    .zone {
      width: 45%;
      min-height: 320px;
      border: 2px dashed #555;
      border-radius: 10px;
      padding: 10px;
      background-color: #1e1e1e;
      transition: background-color 0.3s ease;
      margin-top: 20px;
    }

    .zone:hover {
      background-color: #2a2a2a;
    }

    .zone h3 {
      margin-top: 0;
      color: #00bcd4;
    }

    .email-card {
      width: 90%;
      background-color: #2c2c2c;
      margin: 10px auto;
      padding: 15px;
      border-radius: 10px;
      border-left: 5px solid #03a9f4;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
      cursor: grab;
      color: #f0f0f0;
      transition: transform 0.2s ease;
    }

    .email-card:hover {
      transform: scale(1.02);
    }

    .email-card.dragging {
      opacity: 0.6;
    }

    .feedback {
      margin-top: 10px;
      font-weight: bold;
    }

    .explanation {
      font-size: 14px;
      color: #aaaaaa;
      margin-top: 5px;
    }

    .popup {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0, 0, 0, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      display: none;
      z-index: 9999;
    }

    .popup-content {
      background-color: #2c2c2c;
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 0 15px rgba(0,0,0,0.6);
    }

    .popup-content h2 {
      color: #fff;
    }

    .popup-content p {
      color: #ddd;
    }

    .popup-content button {
      padding: 10px 20px;
      font-size: 16px;
      margin-top: 20px;
      border: none;
      background-color: #00c853;
      color: white;
      cursor: pointer;
      border-radius: 5px;
      transition: background-color 0.2s ease;
    }

    .popup-content button:hover {
      background-color: #00b04d;
    }
  </style>
</head>
<body>

  <h1>Phishing Detection Game</h1>
  <p class="instructions">🧠 Drag each email to the correct box: <strong>Phishing</strong> or <strong>Safe</strong>.</p>

  <div class="game-container">
    <div id="emailList" style="width: 100%;"></div>
    <div class="zone" id="phishingZone" ondragover="allowDrop(event)" ondrop="drop(event, true)">
      <h3>🚨 Phishing</h3>
    </div>
    <div class="zone" id="safeZone" ondragover="allowDrop(event)" ondrop="drop(event, false)">
      <h3>✅ Safe</h3>
    </div>
  </div>

  <div class="popup" id="gameOverPopup">
    <div class="popup-content">
      <h2>🎉 Game Over!</h2>
      <p id="finalScore"></p>
      <button onclick="resetGame()">Play Again</button>
    </div>
  </div>

  <script>
    let emails = [];
    let score = 0;
    let total = 0;

    const allExamples = [
      {
        text: "Your account has been locked. Click here immediately to verify your identity.",
        isPhishing: true,
        explanation: "Creates urgency and demands immediate action."
      },
      {
        text: "Reminder: Your meeting with HR is scheduled for Friday at 10:00 AM.",
        isPhishing: false
      },
      {
        text: "Congratulations! You've won a free cruise. Click to claim your prize.",
        isPhishing: true,
        explanation: "Unbelievable offers are common phishing tactics."
      },
      {
        text: "Your package could not be delivered. Please enter your personal information to reschedule.",
        isPhishing: true,
        explanation: "Requests for personal info in suspicious emails is a red flag."
      },
      {
        text: "Monthly newsletter: 5 ways to improve cybersecurity in your workplace.",
        isPhishing: false
      },
      {
        text: "Click here to reset your password. Your account may have been compromised.",
        isPhishing: true,
        explanation: "Fear-based messages often indicate phishing."
      },
      {
        text: "Lunch menu has been updated. Check the new options available this week.",
        isPhishing: false
      },
      {
        text: "We noticed a new login from Moscow. Is this you?",
        isPhishing: true,
        explanation: "Security alert tactics try to trick users."
      },
    ];

    function shuffle(array) {
      return array.sort(() => Math.random() - 0.5);
    }

    function allowDrop(ev) {
      ev.preventDefault();
    }

    function drag(ev) {
      ev.dataTransfer.setData("text", ev.target.id);
    }

    function drop(ev, isPhishingZone) {
      ev.preventDefault();
      const id = ev.dataTransfer.getData("text");
      const card = document.getElementById(id);
      const email = emails.find(e => e.id === id);

      const feedback = document.createElement("div");
      feedback.className = "feedback";

      if (email.isPhishing === isPhishingZone) {
        score++;
        feedback.style.color = "#00e676";
        feedback.innerText = "✅ Correct!";
      } else {
        feedback.style.color = "#ff5252";
        feedback.innerText = "❌ Incorrect!";
        const explanation = document.createElement("div");
        explanation.className = "explanation";
        explanation.innerText = email.explanation || "This email appears to be safe.";
        feedback.appendChild(explanation);
      }

      card.appendChild(feedback);
      if (isPhishingZone) {
        document.getElementById("phishingZone").appendChild(card);
      } else {
        document.getElementById("safeZone").appendChild(card);
      }

      card.setAttribute("draggable", false);
      card.style.cursor = "default";

      total++;
      if (total === emails.length) {
        setTimeout(() => {
          document.getElementById("finalScore").innerText = `You got ${score} out of ${emails.length} correct.`;
          document.getElementById("gameOverPopup").style.display = "flex";
        }, 500);
      }
    }

    function renderEmails() {
      emails = shuffle(allExamples).slice(0, 5).map((e, i) => ({
        ...e,
        id: "email_" + i
      }));
      const container = document.getElementById("emailList");
      container.innerHTML = "";
      emails.forEach(email => {
        const card = document.createElement("div");
        card.className = "email-card";
        card.id = email.id;
        card.setAttribute("draggable", true);
        card.addEventListener("dragstart", drag);
        card.innerText = email.text;
        container.appendChild(card);
      });
    }

    function resetGame() {
      document.getElementById("gameOverPopup").style.display = "none";
      score = 0;
      total = 0;
      document.getElementById("phishingZone").innerHTML = "<h3>🚨 Phishing</h3>";
      document.getElementById("safeZone").innerHTML = "<h3>✅ Safe</h3>";
      renderEmails();
    }

    renderEmails();
  </script>

</body>
</html>
