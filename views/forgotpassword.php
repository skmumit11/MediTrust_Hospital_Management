<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MediTrust-Forgot Password</title>
    <link rel="stylesheet" href="../assets/style_forgotpassword.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="forgot-container">

    <div class="forgot-header">
        <h1>MediTrust Hospital</h1>
        <p class="tagline">Your Personalized Hospital Management System</p>
    </div>

    <form class="forgot-form"
          action="../controllers/forgotpasswordCheck.php"
          method="post">
        <fieldset>
            <legend>Reset Password</legend>

            <!-- PHP error message -->
            <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="error" style="text-align:center;">'
                     . $_SESSION['error'] .
                     '</p>';
                unset($_SESSION['error']);
            }
            ?>

            <div id="codeMessage"></div>

            <div id="verification-section">
                <table>
                    <tr><td>EMAIL ADDRESS:</td></tr>
                    <tr>
                        <td>
                            <input type="email" name="email" id="email"
                                   placeholder="Enter your registered email" required>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input type="submit" class="btn-secondary"
                                   name="send_code" value="Send Code">
                        </td>
                    </tr>

                    <tr><td>VERIFICATION CODE:</td></tr>
                    <tr>
                        <td>
                            <input type="text" id="code"
                                   name="code" placeholder="Enter verification code">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" id="verifyBtn"
                                    class="btn-secondary">
                                Verify Code
                            </button>
                        </td>
                    </tr>
                </table>
            </div>

            <table id="dynamic-section"></table>

            <div class="back-link">
                <a href="login.php">Back to Login</a>
            </div>

        </fieldset>
    </form>

</div>

<script src="../assets/forgotpassword.js"></script>
</body>
</html>
