<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Breakout</title>
    <link rel="stylesheet" href="breakout.css">
    <link rel="stylesheet" href="../styles/menu.css"> 
</head>
<body>
    <?php include("../phps/menu.php"); ?>
    
    <div class="container">
        <header>
            <h1>Breakout</h1>
        </header>

        <canvas id="board" width="500" height="500"></canvas>
        
        <div class="game-controls">
            <button id="start-btn">Начать игру</button>
        </div>

        <div id="message"></div>

        <div id="score-board">
            <table>
                <thead>
                    <tr>
                        <th>Игрок</th>
                        <th>Рекорд</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $conn = new PDO("mysql:host=localhost;dbname=game;charset=utf8", "root", "");
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $sql = "SELECT username, score FROM high_scores_breakout ORDER BY score DESC LIMIT 10";
                        $result = $conn->query($sql);

                        $has_rows = false;
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            $has_rows = true;
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                            echo "<td>" . (int)$row["score"] . "</td>";
                            echo "</tr>";
                        }

                        if (!$has_rows) {
                            echo "<tr><td colspan='2'>Рекордов пока нет</td></tr>";
                        }
                    } catch (PDOException $err) {
                        echo "<tr><td colspan='2'>Ошибка базы данных</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            const CURRENT_USER = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'player'; ?>";
        </script>
        <script src="breakout.js"></script>
    </div>
</body>
</html>