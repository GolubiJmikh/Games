<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$conn = new mysqli("localhost", "root", "", "game");
$conn->set_charset("utf8");

$username = isset($_SESSION['username']) ? $_SESSION['username'] : ($_POST['username'] ?? 'player');
$score = isset($_POST['score']) ? (int)$_POST['score'] : 0;

$sql = "SELECT score FROM high_scores_snake WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $sql = "INSERT INTO high_scores_snake (username, score) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $score);
    $stmt->execute();
} else {
    $row = $result->fetch_assoc();
    if ($score > $row['score']) {
        $sql = "UPDATE high_scores_snake SET score = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $score, $username);
        $stmt->execute();
    }
}

$stmt->close();
$conn->close();