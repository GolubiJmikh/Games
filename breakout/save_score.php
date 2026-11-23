<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "game";

// Приоритет имени из сессии для безопасности
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = $_POST['username'] ?? 'player';
}

$score = isset($_POST['score']) ? (int)$_POST['score'] : 0;

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Проверяем текущий рекорд пользователя
$sql = "SELECT score FROM high_scores_breakout WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Если записи нет — вставляем новую
    $sql = "INSERT INTO high_scores_breakout (username, score) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $score);

    if ($stmt->execute()) {
        echo "Новый рекорд успешно сохранен";
    } else {
        echo "Ошибка: " . $stmt->error;
    }
} else {
    // Если запись есть — обновляем, только если новый счет выше
    $row = $result->fetch_assoc();
    $high_score = $row['score'];

    if ($score > $high_score) {
        $sql = "UPDATE high_scores_breakout SET score = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $score, $username);

        if ($stmt->execute()) {
            echo "Рекорд успешно обновлен";
        } else {
            echo "Ошибка: " . $stmt->error;
        }
    } else {
        echo "Счет не превышает предыдущий рекорд";
    }
}

$stmt->close();
$conn->close();