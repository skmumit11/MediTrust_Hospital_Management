<<<<<<< HEAD
<?php
session_start();
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
            if (isset($_GET['error'])) {
                if ($_GET['error'] === 'invalid') {
                    echo '<div class="error-message">Invalid username or password</div>';
                } elseif ($_GET['error'] === 'empty') {
                    echo '<div class="error-message">All fields are required</div>';
                }
            }
            ?>

            <table>
                <tr>
                    <td>USERNAME/EMAIL:</td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <input type="text" name="username" autocomplete="username">
                    </td>
                </tr>

                <tr>
                    <td>PASSWORD:</td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <div class="password-field">
                            <input type="password" id="password" name="password" placeholder="Enter Password">
                            <span class="toggle-password" data-target="password">
                                <i class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type="checkbox" name="remember_me" id="remember_me">
                        <label for="remember_me">Remember me</label>
                    </td>
                    <td><a href="forgotpassword.php">Forgot Password?</a></td>
                </tr>

                <tr>
                    <td colspan='2'>
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
</div>

<script src="../assets/showpassword.js"></script>
</body>
</html>
=======

<?php
    session_start();
    //print_r($_SESSION);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Page</title>
<link rel="stylesheet" href="../assets/style_login.css">
</head>
<body>
    <div class="login-container">
        <form action="../controllers/loginCheck.php" method="post">
            <fieldset>
                <legend>Login</legend>
                <table>
                    <tr>
                        <td>USERNAME/EMAIL:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="username" value="">
                        </td>
                    </tr>


                    <tr>
                        <td>PASSWORD:</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="password" name="password" value="">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="checkbox" name="remember_me"> <label> Remember me</label>
                        </td>
                        <td><a href="forgotpassword.php"> Forgot Password?</a></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><input type="submit" name="submit" value="Sign In"></td>
                    </tr>

                    <tr>
                        <td>Don't Have an Account?</td>
                        <td><a href="signup.php"> Sign Up</a></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</body>
</html>
>>>>>>> 398e55f6f2dbf4b37aaf57e9117711dbcccecfcd
