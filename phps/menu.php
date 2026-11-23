<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<input class="menu-icon" type="checkbox" id="menu-icon" name="menu-icon" />
<label for="menu-icon"><span>Меню</span></label>
<nav class="nav">
    <ul class="pt-5">
        <li><a href="http://127.0.0.1/games/phps/all.php" class="a-menu">Главная</a></li>
        <?php
        if (isset($_SESSION['username'])) {
            echo '<li><a href="http://127.0.0.1/games/phps/account.php" class="a-menu">Личный кабинет</a></li>';
            echo '<li><a href="http://127.0.0.1/games/phps/logout.php" class="a-menu">Выйти из аккаунта</a></li>';
        } else {
            echo '<li><a href="http://127.0.0.1/games/phps/enter.php" class="a-menu">Войти в аккаунт</a></li>';
        }
        ?>
        <li><a href="http://127.0.0.1/games/phps/games.php" class="a-menu">Игры</a></li>
    </ul>
</nav>