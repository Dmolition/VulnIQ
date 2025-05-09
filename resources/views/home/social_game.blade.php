<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Social Engineering Game</title>
  <style>
    body {
      background-color: #121212;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #f1f1f1;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .game-container {
      background-color: #1f1f1f;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0, 255, 150, 0.2);
      width: 80%;
      max-width: 800px;
      text-align: center;
    }

    .game-header h1 {
      font-size: 2.5em;
      color: #00ffc8;
      letter-spacing: 1px;
    }

    .progress-bar-container {
      background-color: #333;
      border-radius: 8px;
      overflow: hidden;
      margin: 20px auto;
      height: 18px;
      width: 100%;
    }

    .progress-bar {
      height: 100%;
      background-color: #00ffc8;
      width: 0%;
      transition: width 0.4s ease;
    }

    .question {
      font-size: 1.3em;
      margin: 20px 0;
      color: #ffffff;
    }

    .options {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-bottom: 10px;
    }

    .option-card {
      background-color: #2c2c2c;
      padding: 18px 20px;
      border-radius: 8px;
      cursor: pointer;
      transition: transform 0.2s, background-color 0.2s;
      border: 2px solid transparent;
    }

    .option-card:hover {
      background-color: #383838;
      transform: scale(1.02);
      border-color: #00ffc8;
    }

    .feedback {
      font-size: 1.1em;
      color: #ff6b6b;
      margin-top: 15px;
      font-style: italic;
      min-height: 24px;
    }

    .score, .question-count {
      font-size: 1.1em;
      color: #00ffc8;
      margin-top: 10px;
    }

    .restart-button {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #00ffc8;
      color: #121212;
      border: none;
      border-radius: 8px;
      font-size: 1em;
      cursor: pointer;
    }

    .restart-button:hover {
      background-color: #00e6b2;
    }
  </style>
</head>
<body>

  <div class="game-container">
    <div class="game-header">
      <h1>🕵️‍♂️ Social Engineering Awareness</h1>
    </div>

    <div class="progress-bar-container">
      <div class="progress-bar" id="progressBar"></div>
    </div>

    <div class="question-count" id="questionCount">Question 1 of X</div>
    <div class="question" id="question">Loading question...</div>
    <div class="options" id="options"></div>
    <div class="feedback" id="feedback"></div>
    <div class="score" id="score">Score: 0</div>
    <button class="restart-button" id="restartButton" style="display: none;">🔁 Restart Game</button>
  </div>

  <!-- Optional Sound Effects -->
  <audio id="correctSound" src="https://www.soundjay.com/buttons/sounds/button-10.mp3" preload="auto"></audio>
  <audio id="incorrectSound" src="https://www.soundjay.com/buttons/sounds/button-16.mp3" preload="auto"></audio>

  <script>
    const scenarios = [
      {
        question: "You receive a calendar invite from a colleague with a suspicious-looking Zoom link.",
        options: [
          { answer: "Click to join the meeting early", correct: false, feedback: "🚫 Always verify unexpected meeting links — they may lead to credential harvesting." },
          { answer: "Verify the invite with the colleague via internal chat", correct: true, feedback: "✅ Well done! Always confirm unfamiliar calendar invites." }
        ]
      },
      {
        question: "A LinkedIn connection you've never met sends a PDF job offer attachment.",
        options: [
          { answer: "Open the PDF to check the opportunity", correct: false, feedback: "🚫 PDFs can carry malware. Don’t open unsolicited files from unknown sources." },
          { answer: "Research the sender's company and verify via official channels", correct: true, feedback: "✅ Smart thinking! Verification is key." }
        ]
      },
      {
        question: "An attacker clones your company's login page and sends it via an internal-looking email.",
        options: [
          { answer: "Click and enter your login to be safe", correct: false, feedback: "🚫 That’s a classic phishing page. Check URLs carefully." },
          { answer: "Hover over the link and check the URL before proceeding", correct: true, feedback: "✅ Good practice! Always inspect URLs before clicking." }
        ]
      },
      {
        question: "An attacker sends a fake MFA prompt while you're logging in.",
        options: [
          { answer: "Approve it quickly — it must be related", correct: false, feedback: "🚫 MFA fatigue attacks rely on your speed. Always think before approving." },
          { answer: "Deny the request and report the suspicious login attempt", correct: true, feedback: "✅ Excellent! Protect your account by denying unexpected prompts." }
        ]
      },
      {
        question: "You’re asked by 'Finance' to review an urgent invoice from a new third-party domain.",
        options: [
          { answer: "Open it immediately and forward to accounting", correct: false, feedback: "🚫 This could be a spoofed domain with malware." },
          { answer: "Check if the sender domain is legitimate and confirm internally", correct: true, feedback: "✅ Exactly. Always confirm with your team." }
        ]
      },
      {
        question: "You receive a Slack message from a new intern asking for the Wi-Fi credentials.",
        options: [
          { answer: "Send them the credentials directly", correct: false, feedback: "🚫 Internal messaging tools can be exploited if accounts are compromised." },
          { answer: "Redirect them to official IT support channels", correct: true, feedback: "✅ Good move. Use proper procedures for sensitive info." }
        ]
      },
      {
        question: "A USB labeled 'Q4 Salary Info' is found in the parking lot.",
        options: [
          { answer: "Plug it in to see what's on it", correct: false, feedback: "🚫 This is a classic baiting technique. It could infect your system." },
          { answer: "Report it to security and never plug it in", correct: true, feedback: "✅ Perfect response. Never interact with unknown hardware." }
        ]
      },
      {
        question: "You get a voicemail saying your bank account has been suspended and to call back immediately.",
        options: [
          { answer: "Call back and verify your details", correct: false, feedback: "🚫 This is a vishing attack — avoid calling back unverified numbers." },
          { answer: "Check your account status via official bank channels", correct: true, feedback: "✅ Yes! Always use known contact methods." }
        ]
      }
    ];

    let currentScenarioIndex = 0;
    let score = 0;

    function loadScenario(index) {
      const scenario = scenarios[index];
      document.getElementById("question").textContent = scenario.question;
      const optionsEl = document.getElementById("options");
      const feedbackEl = document.getElementById("feedback");
      const questionCountEl = document.getElementById("questionCount");
      const progressBar = document.getElementById("progressBar");

      optionsEl.innerHTML = '';
      feedbackEl.textContent = '';
      questionCountEl.textContent = `Question ${index + 1} of ${scenarios.length}`;
      progressBar.style.width = `${((index + 1) / scenarios.length) * 100}%`;

      scenario.options.forEach(option => {
        const card = document.createElement("div");
        card.className = "option-card";
        card.textContent = option.answer;
        card.onclick = () => handleAnswer(option, feedbackEl);
        optionsEl.appendChild(card);
      });
    }

    function handleAnswer(option, feedbackEl) {
      feedbackEl.textContent = option.feedback;

      const correctSound = document.getElementById("correctSound");
      const incorrectSound = document.getElementById("incorrectSound");

      if (option.correct) {
        score++;
        document.getElementById("score").textContent = "Score: " + score;
        correctSound.play();
      } else {
        incorrectSound.play();
      }

      setTimeout(() => {
        currentScenarioIndex++;
        if (currentScenarioIndex < scenarios.length) {
          loadScenario(currentScenarioIndex);
        } else {
          document.getElementById("question").textContent = "🎉 You've completed all scenarios!";
          document.getElementById("options").innerHTML = '';
          feedbackEl.textContent = '';
          document.getElementById("questionCount").textContent = '';
          document.getElementById("restartButton").style.display = "inline-block";
        }
      }, 1400);
    }

    document.getElementById("restartButton").addEventListener("click", () => {
      currentScenarioIndex = 0;
      score = 0;
      document.getElementById("score").textContent = "Score: 0";
      document.getElementById("restartButton").style.display = "none";
      loadScenario(currentScenarioIndex);
    });

    // Initialize the game
    loadScenario(currentScenarioIndex);
  </script>

</body>
</html>
