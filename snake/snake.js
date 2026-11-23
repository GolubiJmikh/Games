var blockSize = 25;
var rows = 18;
var cols = 18;
var board, context;
var snakeX = blockSize * 5;
var snakeY = blockSize * 5;
var vX = 0, vY = 0;
var snakeBody = [];
var foodX, foodY;
var score = 0;
var gameOver = false;

window.onload = function () {
    board = document.getElementById("board");
    board.height = rows * blockSize;
    board.width = cols * blockSize;
    context = board.getContext("2d");
    placeFood();
    document.addEventListener("keydown", changeDirection);
    setInterval(update, 1000 / 10);
}

function update() {
    if (gameOver) return;

    context.fillStyle = "black";
    context.fillRect(0, 0, board.width, board.height);

    context.fillStyle = "red";
    context.fillRect(foodX, foodY, blockSize, blockSize);

    if (snakeX == foodX && snakeY == foodY) {
        snakeBody.push([foodX, foodY]);
        score++;
        placeFood();
    }

    for (let i = snakeBody.length - 1; i > 0; i--) {
        snakeBody[i] = snakeBody[i - 1];
    }
    if (snakeBody.length) {
        snakeBody[0] = [snakeX, snakeY];
    }

    context.fillStyle = "lime";
    snakeX += vX * blockSize;
    snakeY += vY * blockSize;
    context.fillRect(snakeX, snakeY, blockSize, blockSize);
    for (let i = 0; i < snakeBody.length; i++) {
        context.fillRect(snakeBody[i][0], snakeBody[i][1], blockSize, blockSize);
    }

    // Проверка столкновения со стенами
    if (snakeX < 0 || snakeX >= cols * blockSize || snakeY < 0 || snakeY >= rows * blockSize) {
        endGame();
    }

    // Проверка столкновения с телом
    for (let i = 0; i < snakeBody.length; i++) {
        if (snakeX == snakeBody[i][0] && snakeY == snakeBody[i][1]) {
            endGame();
        }
    }
}

function endGame() {
    gameOver = true;
    let finalName = typeof CURRENT_USER !== 'undefined' ? CURRENT_USER : 'player';
    alert("Игра окончена, " + finalName + "! Ваши очки: " + score);
    saveScore(finalName, score);
}

function changeDirection(e) {
    if (e.code == "ArrowUp" && vY != 1) { vX = 0; vY = -1; }
    else if (e.code == "ArrowDown" && vY != -1) { vX = 0; vY = 1; }
    else if (e.code == "ArrowLeft" && vX != 1) { vX = -1; vY = 0; }
    else if (e.code == "ArrowRight" && vX != -1) { vX = 1; vY = 0; }
}

function placeFood() {
    foodX = Math.floor(Math.random() * cols) * blockSize;
    foodY = Math.floor(Math.random() * rows) * blockSize;
}

function saveScore(name, finalScore) {
    let formData = new FormData();
    formData.append("username", name);
    formData.append("score", finalScore);

    fetch("save_score_snake.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log("Сервер ответил:", data);
        location.reload();
    })
    .catch(error => {
        console.error("Ошибка сохранения:", error);
        location.reload();
    });
}