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
    <title>Вход</title>
    <link rel="stylesheet" href="../styles/style-reg-ent.css">
</head>
<body>
    <div class="reg-container">
        <form class="reg-form" method="post">
            <h2>Войти в аккаунт</h2>
            <div class="input-group">
                <label for="login">Логин</label>
                <input type="text" id="login" name="login" required placeholder="Введите логин">
            </div>
            <div class="input-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="submit-btn">Войти</button>
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $login = htmlentities($_POST['login']);
        $password = htmlentities($_POST['password']);
        
        try {
            $conn = new PDO("mysql:host=localhost;dbname=game;charset=utf8", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM user WHERE username = :login AND user_password = :password");
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['username'] = $row['username'];
                header("Location: account.php");
                exit();
            } else {
                header("Location: auth.php");
                exit();
            }
        } catch (PDOException $err) {
            echo "<p style='color:red; text-align:center;'>Ошибка подключения: " . $err->getMessage() . "</p>";
        }
    }
    ?>
</body>
</html>