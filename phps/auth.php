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
    <title>Регистрация</title>
    <link rel="stylesheet" href="../styles/style-reg-ent.css">
</head>
<body>
    <div class="reg-container">
        <form class="reg-form" method="post">
            <h2>Создать аккаунт</h2>
            <div class="input-group">
                <label for="login">Логин</label>
                <input type="text" id="login" name="login" required placeholder="Введите логин">
            </div>
            <div class="input-group">
                <label for="email">Почта</label>
                <input type="email" id="email" name="email" required placeholder="email@example.com">
            </div>
            <div class="input-group">
                <label for="password">Пароль</label>
                <input type="text" id="password" name="password" required placeholder="Введите пароль">
            </div>
            <div class="input-group">
                <label for="confirm_password">Подтверждение пароля</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
                <span id="error-message" class="error-text" style="display:none; color:red;">Пароли не совпадают</span>
            </div>
            <button type="submit" class="submit-btn">Зарегистрироваться</button>
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $login = htmlentities($_POST['login']);
        $email = htmlentities($_POST['email']);
        $password = htmlentities($_POST['password']);
        try {
            $conn = new PDO("mysql:host=localhost;dbname=game;charset=utf8", "root", "");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sql = "INSERT INTO user (username, user_password, mail) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$login, $password, $email]);
            
            header("Location: enter.php");
            exit;
        } catch (PDOException $err) {
            echo "<p style='color:red; text-align:center;'>Ошибка: " . $err->getMessage() . "</p>";
        }
    }
    ?>

    <script>
        const form = document.querySelector('.reg-form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const errorMessage = document.getElementById('error-message');

        form.addEventListener('submit', function(event) {
            errorMessage.style.display = 'none';
            if (password.value !== confirmPassword.value) {
                event.preventDefault();
                errorMessage.style.display = 'block';
                confirmPassword.focus();
            }
        });
    </script>
</body>
</html>