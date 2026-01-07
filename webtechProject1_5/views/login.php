
<?php
session_start();

$prefill = '';

if(isset($_SESSION['old_username'])){
    $prefill = $_SESSION['old_username'];
    unset($_SESSION['old_username']);
} elseif(isset($_COOKIE['remember_identity'])){
    $prefill = $_COOKIE['remember_identity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MediTrust-Login</title>
    <link rel="stylesheet" href="../assets/style_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <h1>MediTrust Hospital</h1>
        <p class="tagline">Your Personalized Hospital Management System</p>
    </div>

    <form action="../controllers/loginCheck.php" method="post">
        <fieldset>
            <legend>Login</legend>

            <?php
                if(isset($_SESSION['errors']) && !empty($_SESSION['errors'])){
                    echo '<div class="error-message">';
                    foreach($_SESSION['errors'] as $error){
                        echo "<p>$error</p>";
                    }
                    echo '</div>';
                    unset($_SESSION['errors']);
                }
            ?>

            <table>
                <tr>
                    <td>USERNAME/EMAIL:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input
                            type="text"
                            name="username"
                            placeholder="Enter Username/Email"
                            autocomplete="username"
                            value="<?php echo htmlspecialchars($prefill); ?>"
                        >
                    </td>
                </tr>

                <tr>
                    <td>PASSWORD:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="password-field">
                            <input type="password" id="password" name="password" placeholder="Enter Password" autocomplete="current-password">
                            <span class="toggle-password" data-target="password">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type="checkbox" name="remember_me" id="remember_me" <?php echo isset($_COOKIE['remember_identity']) ? 'checked' : ''; ?>>
                        <label for="remember_me">Remember me</label>
                    </td>
                    <td><a href="forgotpassword.php">Forgot Password?</a></td>
                </tr>

                <tr>
                    <td colspan="2">
                        <input type="submit" class="btn" name="submit" value="Log In">
                    </td>
                </tr>

                <tr>
                    <td>Don't Have an Account?</td>
                    <td><a href="signup.php">Sign Up</a></td>
                </tr>
            </table>
        </fieldset>
    </form>

    <button class="btn-back" onclick="window.location.href='home.php'">Back Home</button>
</div>

<script src="../assets/showpassword.js"></script>
</body>
</html>

