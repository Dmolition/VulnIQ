<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Data Defender: Snake Edition</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      background-color: #1e272e;
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    canvas {
      background-color: #2f3640;
      border: 5px solid #00cec9;
      border-radius: 10px;
    }

    /* Pop-up styles */
    .popup {
      position: fixed;
      top: 20%;
      left: 50%;
      transform: translateX(-50%);
      background-color: #2f3640;
      color: #fff;
      padding: 20px;
      border-radius: 10px;
      border: 2px solid #00cec9;
      font-size: 18px;
      display: none;
      z-index: 1000;
    }
  </style>
</head>
<body>

<canvas id="gameCanvas" width="600" height="600"></canvas>

<div id="popup" class="popup">
  <h3>Ransomware Mitigation Tip</h3>
  <p id="tipText"></p>
  <button onclick="closePopup()">Close</button>
</div>

<script>
  const canvas = document.getElementById("gameCanvas");
  const ctx = canvas.getContext("2d");

  const gridSize = 20;
  const tileCount = canvas.width / gridSize;

  let snake = [{ x: 10, y: 10 }];
  let direction = { x: 0, y: 0 };
  let backup = getRandomPosition();
  let ransomware = getRandomPosition();
  let score = 0;
  let gameSpeed = 150;
  let lastTime = 0;
  let gameStarted = false;

  let backupCounter = 0; // Counter for tracking backups

  // Array of ransomware mitigation tips
  const tips = [
    "Keep your software and operating systems updated.",
    "Use strong, unique passwords for every account.",
    "Backup your important files regularly.",
    "Be cautious of suspicious emails and attachments.",
    "Use reliable security software and keep it up-to-date."
  ];

  document.addEventListener("keydown", (e) => {
    if (!gameStarted) {
      gameStarted = true; // start only after first arrow key press
    }

    switch (e.key) {
      case "ArrowUp":
        if (direction.y === 0) direction = { x: 0, y: -1 };
        break;
      case "ArrowDown":
        if (direction.y === 0) direction = { x: 0, y: 1 };
        break;
      case "ArrowLeft":
        if (direction.x === 0) direction = { x: -1, y: 0 };
        break;
      case "ArrowRight":
        if (direction.x === 0) direction = { x: 1, y: 0 };
        break;
    }
  });

  function getRandomPosition() {
    return {
      x: Math.floor(Math.random() * tileCount),
      y: Math.floor(Math.random() * tileCount)
    };
  }

  function drawTile(x, y, iconClass) {
    ctx.save();
    ctx.translate(x * gridSize + gridSize / 2, y * gridSize + gridSize / 2); // Position in the middle
    ctx.scale(1.2, 1.2); // Scale the icon to make it bigger
    ctx.font = "30px FontAwesome"; // Increased font size for bigger icons
    ctx.fillText(iconClass, -20, 20); // Adjust the position to center the icon
    ctx.restore();
  }

  function gameOver() {
    alert(`💀 Ransomware attack! Game Over.\n💾 Backups collected: ${score}\n🔐 Tip: Backups are your best defense!`);
    snake = [{ x: 10, y: 10 }];
    direction = { x: 0, y: 0 };
    backup = getRandomPosition();
    ransomware = getRandomPosition();
    score = 0;
    gameSpeed = 150;
    gameStarted = false;
  }

  function showMitigationTips() {
    const popup = document.getElementById('popup');
    const tipText = document.getElementById('tipText');

    // Select a random tip from the array
    const randomIndex = Math.floor(Math.random() * tips.length);
    tipText.textContent = tips[randomIndex]; // Display the random tip

    popup.style.display = 'block'; // Show the pop-up

    // Automatically hide the pop-up after 5 seconds
    setTimeout(closePopup, 5000);
  }

  function closePopup() {
    const popup = document.getElementById('popup');
    popup.style.display = 'none'; // Hide the pop-up
  }

  function update() {
    const head = {
      x: snake[0].x + direction.x,
      y: snake[0].y + direction.y
    };

    if (direction.x === 0 && direction.y === 0) return;

    if (
      head.x < 0 || head.y < 0 ||
      head.x >= tileCount || head.y >= tileCount ||
      snake.some(segment => segment.x === head.x && segment.y === head.y)
    ) {
      return gameOver();
    }

    if (head.x === ransomware.x && head.y === ransomware.y) {
      return gameOver();
    }

    snake.unshift(head);

    if (head.x === backup.x && head.y === backup.y) {
      score++;
      backupCounter++;

      // Every 3 backups, show a random mitigation tip
      if (backupCounter >= 3) {
        showMitigationTips();
        backupCounter = 0; // Reset the counter
      }

      backup = getRandomPosition();
      ransomware = getRandomPosition();
      if (gameSpeed > 50) gameSpeed -= 5;
    } else {
      snake.pop();
    }
  }

  function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Draw snake
    snake.forEach((segment, i) => {
      drawTile(segment.x, segment.y, i === 0 ? "🧑" : "🌀"); // 🧑 for head (person), 🌀 for body
    });

    drawTile(backup.x, backup.y, "🛡️"); // Shield icon for backup
    drawTile(ransomware.x, ransomware.y, "🦠"); // Virus icon for ransomware

    ctx.fillStyle = "#ffffff";
    ctx.font = "18px Arial";
    ctx.fillText("Backups: " + score, 10, 20);

    if (!gameStarted) {
      ctx.fillStyle = "#ffffff";
      ctx.font = "24px Arial";
      ctx.fillText("Press any arrow key to start", 150, 300);
    }
  }

  function gameLoop(timestamp) {
    if (timestamp - lastTime > gameSpeed) {
      if (gameStarted) update();
      draw();
      lastTime = timestamp;
    } else {
      if (!gameStarted) draw();
    }
    requestAnimationFrame(gameLoop);
  }

  requestAnimationFrame(gameLoop);
</script>

</body>
</html>
