let currentPlayer = "X";
let board = ["", "", "", "", "", "", "", "", ""];
let gameEnded = false;

const winPatterns = [
    [0, 1, 2], [3, 4, 5], [6, 7, 8],
    [0, 3, 6], [1, 4, 7], [2, 5, 8],
    [0, 4, 8], [2, 4, 6]
];

function cellClicked(cellIndex) {
    if (!gameEnded && board[cellIndex] === "") {
        makeMove(cellIndex, "X");

        if (!gameEnded) {
            currentPlayer = "0";
            setTimeout(smartBotMove, 400);
        }
    }
}

function smartBotMove() {
    if (gameEnded) return;

    let bestScore = -Infinity;
    let move;

    for (let i = 0; i < board.length; i++) {
        if (board[i] === "") {
            board[i] = "0";
            let score = minimax(board, 0, false);
            board[i] = "";
            if (score > bestScore) {
                bestScore = score;
                move = i;
            }
        }
    }

    if (move !== undefined) {
        makeMove(move, "0");
        currentPlayer = "X";
    }
}

function minimax(tempBoard, depth, isMaximizing) {
    if (checkWinnerInternal(tempBoard, "0")) return 10 - depth;
    if (checkWinnerInternal(tempBoard, "X")) return depth - 10;
    if (tempBoard.every(cell => cell !== "")) return 0;

    if (isMaximizing) {
        let bestScore = -Infinity;
        for (let i = 0; i < tempBoard.length; i++) {
            if (tempBoard[i] === "") {
                tempBoard[i] = "0";
                bestScore = Math.max(minimax(tempBoard, depth + 1, false), bestScore);
                tempBoard[i] = "";
            }
        }
        return bestScore;
    } else {
        let bestScore = Infinity;
        for (let i = 0; i < tempBoard.length; i++) {
            if (tempBoard[i] === "") {
                tempBoard[i] = "X";
                bestScore = Math.min(minimax(tempBoard, depth + 1, true), bestScore);
                tempBoard[i] = "";
            }
        }
        return bestScore;
    }
}

function makeMove(index, player) {
    const cell = document.getElementById(`cell${index}`);
    cell.textContent = player;
    cell.setAttribute('data-value', player);
    board[index] = player;

    if (checkWinnerInternal(board, player)) {
        document.getElementById("message").textContent = player === "X" ? "Вы победили!" : "Бот победил!";
        gameEnded = true;
    } else if (board.every(cell => cell !== "")) {
        document.getElementById("message").textContent = "Ничья!";
        gameEnded = true;
    }
}

function checkWinnerInternal(currentBoard, player) {
    return winPatterns.some(pattern => 
        pattern.every(index => currentBoard[index] === player)
    );
}