<div class="overlay-menu" id="overlayMenu">
    <div class="hamburger-content">
        <div>
            <a href="../Pages/menu.php">Menu</a>
            <hr>
            <a href="../Pages/account_overview.php">Account</a>
            <hr>
            <a href="../Pages/cart.php">Cart</a>
            <hr>
        </div>
        <div>
            <?php
            if (!isset($_SESSION['user_id'])) {
                echo '<button class="nav-log-button" onclick="location.href=\'login.php\'">Login</button>';
                echo '<button class="nav-log-button" style="margin-left:3px;background-color: #3498db;" onclick="location.href=\'create_account.php\'">Create Account</button>';
            } else {
                echo '<form action="../PHP_Handling/login_handler.php" method="post">';
                echo '<button type="submit" class="nav-log-button" name="try_logout" >' . $_SESSION['user_name'] . ' - Logout</button>';
                echo '</form>';
            }

            ?>
        </div>
    </div>

</div>
