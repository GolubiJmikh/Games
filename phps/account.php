<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../styles/profile-styles.css">
</head>

<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $conn = new mysqli("localhost", "root", "", "game");
    $conn->set_charset("utf8mb4");

    if (!isset($_SESSION['username'])) {
        header("Location: enter.php");
        exit();
    }
    $currentUser = $_SESSION['username'];

    if (isset($_POST['upload_avatar']) && isset($_FILES['avatar'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileExtension = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
        $newFileName = bin2hex(random_bytes(10)) . "." . $fileExtension;
        $targetFile = $targetDir . $newFileName;
        $check = getimagesize($_FILES["avatar"]["tmp_name"]);
        if ($check !== false && $_FILES["avatar"]["size"] < 2000000) {
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)) {
                $stmt = $conn->prepare("UPDATE user SET avatar = ? WHERE username = ?");
                $stmt->bind_param("ss", $targetFile, $currentUser);
                $stmt->execute();
            }
        }
    }

    if (isset($_POST['delete_avatar'])) {
        $stmt = $conn->prepare("UPDATE user SET avatar = '../uploads/default.jpg' WHERE username = ?");
        $stmt->bind_param("s", $currentUser);
        $stmt->execute();
    }

    $userQuery = $conn->prepare("SELECT mail, dates, avatar FROM user WHERE username = ?");
    $userQuery->bind_param("s", $currentUser);
    $userQuery->execute();
    $userData = $userQuery->get_result()->fetch_assoc();

    $avatarName = $userData['avatar'];

    if (empty($avatarName) || $avatarName == 'default.jpg' || !file_exists("../uploads/" . $avatarName)) {
        $avatarPath = "../uploads/default.jpg";
    } else {
        $avatarPath = "../uploads/" . $avatarName;
    }
    $stmtBreakout = $conn->prepare("SELECT MAX(score) as high_score FROM high_scores_breakout WHERE username = ?");
    $stmtBreakout->bind_param("s", $currentUser);
    $stmtBreakout->execute();
    $breakoutScore = $stmtBreakout->get_result()->fetch_assoc()['high_score'] ?? 0;

    $stmtSnake = $conn->prepare("SELECT MAX(score) as high_score FROM high_scores_snake WHERE username = ?");
    $stmtSnake->bind_param("s", $currentUser);
    $stmtSnake->execute();
    $snakeScore = $stmtSnake->get_result()->fetch_assoc()['high_score'] ?? 0;
    ?>

    <div class="profile-info">
        <h2>Профиль: <?php echo htmlspecialchars($currentUser); ?></h2>
        <div class="avatar-container">
            <img src="<?php echo htmlspecialchars($avatarPath); ?>" alt="Avatar" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
        </div>
        <p>Аккаунт создан: <?php echo htmlspecialchars($userData['dates']); ?></p>
        <p>Ваш Email: <?php echo htmlspecialchars($userData['mail']); ?></p>

        <div class="stats-container">
            <h3>Ваши рекорды</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-label">Змейка:</span>
                    <span class="stat-value"><?php echo $snakeScore; ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Breakout:</span>
                    <span class="stat-value"><?php echo $breakoutScore; ?></span>
                </div>
            </div>
        </div>

        <div class="control-panel">
            <form action="" method="post" enctype="multipart/form-data" class="upload-form">
                <label for="avatar" class="file-label" id="file-name">📁 Выбрать изображение</label>
                <input type="file" name="avatar" id="avatar" accept="image/*" required>
                <button type="submit" name="upload_avatar" class="submit-btn">Обновить картинку</button>
            </form>
            <form action="" method="post" class="delete-form">
                <button type="submit" name="delete_avatar">Удалить картинку</button>
            </form>
            <a href='games.php'>К играм</a>
            <a href="logout.php">Выйти</a>
        </div>
    </div>

    <script>
        document.getElementById('avatar').onchange = function() {
            let fileName = this.value.split('\\').pop();
            document.getElementById('file-name').innerHTML = '✅ ' + (fileName ? fileName : 'Файл выбран');
        };
    </script>
</body>

</html>