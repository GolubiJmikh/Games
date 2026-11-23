let board;
let bwidth = 500;
let bheight = 500;
let context;
let gameStarted = false;
let gameInterval;

let playerWidth = 80;
let playerHeight = 10;
let playervX = 10;

let player = {
    x: bwidth / 2 - playerWidth / 2,
    y: bheight - playerHeight - 5,
    width: playerWidth,
    height: playerHeight,
    vX: playervX
};

let ballWidth = 10;
let ballHeight = 10;
let ballvX = 3;
let ballvY = 2;

let ball = {
    x: bwidth / 2,
    y: bheight / 2,
    width: ballWidth,
    height: ballHeight,
    vX: ballvX,
    vY: ballvY
};

let blockArray = [];
let blockWidth = 50;
let blockHeight = 10;
let blockColumns = 8;
let blockRows = 3;
let blockCount = 0;
let score = 0;
let gameOver = false;

window.onload = function () {
    board = document.getElementById("board");
    board.width = bwidth;
    board.height = bheight;
    context = board.getContext("2d");

    createBlocks();
    drawInitialState();

    document.getElementById("start-btn").addEventListener("click", function () {
        if (!gameStarted) {
            startGame();
        }
    });

    document.addEventListener("keydown", movePlayer);
};

function startGame() {
    if (gameInterval) {
        clearInterval(gameInterval);
    }
    resetGame();
    gameStarted = true;
    gameOver = false;
    score = 0;
    gameInterval = setInterval(update, 1000 / 50);
}

function drawInitialState() {
    context.clearRect(0, 0, board.width, board.height);
    context.fillStyle = "lightgreen";
    context.fillRect(player.x, player.y, player.width, player.height);
    context.fillStyle = "white";
    context.fillRect(ball.x, ball.y, ball.width, ball.height);
    context.fillStyle = "skyblue";
    for (let block of blockArray) {
        context.fillRect(block.x, block.y, block.width, block.height);
    }
}

function update() {
    if (gameOver) return;
    context.clearRect(0, 0, board.width, board.height);

    context.fillStyle = "lightgreen";
    context.fillRect(player.x, player.y, player.width, player.height);

    context.fillStyle = "white";
    ball.x += ball.vX;
    ball.y += ball.vY;
    context.fillRect(ball.x, ball.y, ball.width, ball.height);

    if (ball.y <= 0) {
        ball.vY *= -1;
    } else if (ball.x <= 0 || (ball.x + ball.width >= bwidth)) {
        ball.vX *= -1;
    } else if (ball.y + ball.height >= bheight) {
        gameOver = true;
        gameStarted = false;
        clearInterval(gameInterval);
        let finalName = CURRENT_USER;
        alert("Игра окончена! Ваш результат сохранен, " + finalName);
        saveScore(finalName, score);
    }

    if (tCollision(ball, player) || bCollision(ball, player)) {
        ball.vY *= -1;
    } else if (lCollision(ball, player) || rCollision(ball, player)) {
        ball.vX *= -1;
    }

    context.fillStyle = "skyblue";
    for (let i = 0; i < blockArray.length; i++) {
        let block = blockArray[i];
        if (!block.break) {
            if (tCollision(ball, block) || bCollision(ball, block)) {
                block.break = true;
                ball.vY *= -1;
                score += 10;
                blockCount -= 1;
            } else if (lCollision(ball, block) || rCollision(ball, block)) {
                block.break = true;
                ball.vX *= -1;
                score += 10;
                blockCount -= 1;
            }
            context.fillRect(block.x, block.y, block.width, block.height);
        }
    }

    context.font = "20px sans-serif";
    context.fillText("Счет: " + score, 10, 25);
}

function movePlayer(e) {
    if (e.code == "ArrowLeft" && player.x > 0) {
        player.x -= player.vX;
    } else if (e.code == "ArrowRight" && (player.x + player.width) < bwidth) {
        player.x += player.vX;
    }
}

function resetGame() {
    player.x = bwidth / 2 - playerWidth / 2;
    ball.x = bwidth / 2;
    ball.y = bheight / 2;
    ball.vX = ballvX;
    ball.vY = ballvY;
    createBlocks();
}

function Collision(a, b) {
    return a.x < b.x + b.width &&
        a.x + a.width > b.x &&
        a.y < b.y + b.height &&
        a.y + a.height > b.y;
}

function tCollision(ball, block) {
    return Collision(ball, block) && (ball.y + ball.height) >= block.y;
}

function bCollision(ball, block) {
    return Collision(ball, block) && (block.y + block.height) >= ball.y;
}

function lCollision(ball, block) {
    return Collision(ball, block) && (ball.x + ball.width) >= block.x;
}

function rCollision(ball, block) {
    return Collision(ball, block) && (block.x + block.width) >= ball.x;
}

function createBlocks() {
    blockArray = [];
    for (let c = 0; c < blockColumns; c++) {
        for (let r = 0; r < blockRows; r++) {
            let block = {
                x: 5 + c * (blockWidth + 10),
                y: 45 + r * (blockHeight + 10),
                width: blockWidth,
                height: blockHeight,
                break: false
            };
            blockArray.push(block);
        }
    }
    blockCount = blockArray.length;
}

function saveScore(name, finalScore) {
    let formData = new FormData();
    formData.append("username", name);
    formData.append("score", finalScore);

    fetch("save_score.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        location.reload();
    })
    .catch(error => {
        alert("Не удалось сохранить рекорд.");
    });
}