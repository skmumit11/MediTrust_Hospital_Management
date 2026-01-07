<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MediTrust-Signup</title>
    <link rel="stylesheet" href="../assets/style_signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="signup-header">
    <h1>MediTrust Hospital</h1>
    <p class="tagline">Your Personalized Hospital Management System</p>
</div>

<div class="signup-container">



    <form name="signupForm" class="signup-form" action="../controllers/signupCheck.php" method="post" onsubmit="return validateSignupForm();">
        
        <!-- Display errors -->
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
        <fieldset>
            <legend>Signup Page</legend>
            <table>
                <!-- Full Name -->
                <tr><td>FULL NAME:</td></tr>
                <tr>
                    <td>
                        <input type="text" name="fullname" placeholder="ENTER FULL NAME" value="<?php echo $_SESSION['old']['fullname'] ?? ''; ?>">
                        <span class="error-msg" id="fullnameError"></span>
                    </td>
                </tr>
                
                <!-- Username -->
                <tr><td>USERNAME:</td></tr>
                <tr>
                    <td>
                        <input type="text" name="username" placeholder="ENTER UNIQUE USERNAME" value="<?php echo $_SESSION['old']['username'] ?? ''; ?>">
                        <span class="error-msg" id="usernameError"></span>
                    </td>
                </tr>
                
                <!-- Email -->
                <tr><td>EMAIL:</td></tr>
                <tr>
                    <td>
                        <input type="email" name="email" placeholder="example@email.com" value="<?php echo $_SESSION['old']['email'] ?? ''; ?>">
                        <span class="error-msg" id="emailError"></span>
                    </td>
                </tr>
                
                <!-- DOB -->
                <tr><td>DATE OF BIRTH:</td></tr>
                <tr>
                    <td>
                        <input type="date" name="dob" value="<?php echo $_SESSION['old']['dob'] ?? ''; ?>">
                        <span class="error-msg" id="dobError"></span>
                    </td>
                </tr>
                
                <!-- Gender -->
                <tr><td>GENDER:</td></tr>
                <tr>
                    <td>
                        <input type="radio" name="gender" value="Male" <?php if(isset($_SESSION['old']['gender']) && $_SESSION['old']['gender']=='Male') echo 'checked'; ?>> Male <br>
                        <input type="radio" name="gender" value="Female" <?php if(isset($_SESSION['old']['gender']) && $_SESSION['old']['gender']=='Female') echo 'checked'; ?>> Female <br>
                        <input type="radio" name="gender" value="Other" <?php if(isset($_SESSION['old']['gender']) && $_SESSION['old']['gender']=='Other') echo 'checked'; ?>> Other <br>
                        <span class="error-msg" id="genderError"></span>
                    </td>
                </tr>
                
                <!-- Address -->
                <tr><td>ADDRESS:</td></tr>
                <tr>
                    <td>
                        <textarea name="address" rows="4" cols="30" placeholder="Enter your address"><?php echo $_SESSION['old']['address'] ?? ''; ?></textarea>
                        <span class="error-msg" id="addressError"></span>
                    </td>
                </tr>
                
                <!-- Password -->
                <tr><td>PASSWORD:</td></tr>
                <tr>
                    <td class="password-field">
                        <input type="password" name="password" id="password" placeholder="ENTER PASSWORD">
                        <span class="toggle-password" data-target="password">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                        <span class="error-msg" id="passwordError"></span>
                    </td>
                </tr>

                <!-- Confirm Password -->
                <tr><td>CONFIRM PASSWORD:</td></tr>
                <tr>
                    <td class="password-field">
                        <input type="password" name="confirmpassword" id="confirmpassword" placeholder="CONFIRM PASSWORD">
                        <span class="toggle-password" data-target="confirmpassword">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                        <span class="error-msg" id="confirmpasswordError"></span>
                    </td>
                </tr>

                <!-- Submit -->
                <tr>
                    <td colspan="2"><input type="submit" class="btn" name="submit" value="Sign up"></td>
                </tr>
                
                <!-- Login Link -->
                <tr>
                    <td colspan="2">Already Have an Account? <a href="login6_10.php">Log In</a></td>
                </tr>
            </table>
        </fieldset>
    </form>
    <button class="btn-back" onclick="window.location.href='home.php'">Back Home</button>

</div>


<script src="../assets/signupValidate.js"></script>
<script src="../assets/showpassword.js"></script>

</body>
</html>

<?php unset($_SESSION['old']); ?>
