let currentPlayer = "X";
let gameEnded = false;
let board = ["", "", "", "", "", "", "", "", ""];

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
            setTimeout(botMove, 500);
        }
    }
}

function botMove() {
    if (gameEnded) return;

    let emptyCells = board.map((val, idx) => val === "" ? idx : null).filter(val => val !== null);

    if (emptyCells.length > 0) {
        const randomIndex = Math.floor(Math.random() * emptyCells.length);
        makeMove(emptyCells[randomIndex], "0");
        currentPlayer = "X";
    }
}

function makeMove(index, player) {
    const cell = document.getElementById(`cell${index}`);
    cell.textContent = player;
    cell.setAttribute('data-value', player);
    board[index] = player;

    if (checkWinner(player)) {
        document.getElementById("message").textContent = `Игрок ${player} победил!`;
        gameEnded = true;
    } else if (isBoardFull()) {
        document.getElementById("message").textContent = "Ничья!";
        gameEnded = true;
    }
}

function checkWinner(player) {
    return winPatterns.some(pattern => 
        pattern.every(index => board[index] === player)
    );
}

function isBoardFull() {
    return board.every(cell => cell !== "");
}