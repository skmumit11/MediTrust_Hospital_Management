<<<<<<< HEAD
<?php 
if(isset($_SESSION['error'])) { 
    echo '<p class="error-msg">'.$_SESSION['error'].'</p>'; 
    unset($_SESSION['error']); 
} 
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
        <fieldset>
            <legend>Signup Page</legend>
            <table>
                <!-- Full Name -->
                <tr><td>FULL NAME:</td></tr>
                <tr>
                    <td>
                        <input type="text" name="fullname" placeholder="ENTER FULL NAME">
                        <span class="error-msg" id="fullnameError"></span>
                    </td>
                </tr>
                
                <!-- Username -->
                <tr><td>USERNAME:</td></tr>
                <tr>
                    <td>
                        <input type="text" name="username" placeholder="ENTER UNIQUE USERNAME">
                        <span class="error-msg" id="usernameError"></span>
                    </td>
                </tr>
                
                <!-- Email -->
                <tr><td>EMAIL:</td></tr>
                <tr>
                    <td>
                        <input type="email" name="email" placeholder="example@email.com">
                        <span class="error-msg" id="emailError"></span>
                    </td>
                </tr>
                
                <!-- DOB -->
                <tr><td>DATE OF BIRTH:</td></tr>
                <tr>
                    <td>
                        <input type="date" name="dob">
                        <span class="error-msg" id="dobError"></span>
                    </td>
                </tr>
                
                <!-- Gender -->
                <tr><td>GENDER:</td></tr>
                <tr>
                    <td>
                        <input type="radio" name="gender" value="Male"> Male <br>
                        <input type="radio" name="gender" value="Female"> Female <br>
                        <input type="radio" name="gender" value="Other"> Other <br>
                        <span class="error-msg" id="genderError"></span>
                    </td>
                </tr>
                
                <!-- Address -->
                <tr><td>ADDRESS:</td></tr>
                <tr>
                    <td>
                        <textarea name="address" rows="4" cols="30" placeholder="Enter your address"></textarea>
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
                    <td colspan=2><input type="submit" class="btn" name="submit" value="Sign up"></td>
                </tr>
                
                <!-- Login Link -->
                <tr>
                    <td colspan=2>Already Have an Account? <a href="login.php">Log In</a></td>
=======
<!--
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Signup Page</title>
</head>
<body>
        <form action="../controllers/signupCheck.php" method="post" enctype="">
            <fieldset>
            <legend>Signup Page</legend>
            <table>
                <tr>
                    <td>FULL NAME: </td>
                    <td><input type="text" name="fullname" value="" placeholder="ENTER FULL NAME"></td>
                </tr>
                <tr>
                    <td>USERNAME: </td>
                    <td><input type="text" name="username" value="" placeholder="ENTER UNIQUE USERNAME"></td>
                </tr>

                <tr>
                    <td>EMAIL: </td>
                    <td><input type="email" name="email" value="" placeholder="example@email.com"></td>
                </tr>

                <tr>
                    <td>DATE OF BIRTH</td>
                
                    <td><input type="date" name="dob"></td>
                </tr>

                <tr>
                    <td>GENDER: </td>
                    <td>
                        <input type="radio" name="gender" value="male"> Male <br>
                        <input type="radio" name="gender" value="female"> Female <br>
                        <input type="radio" name="gender" value="other"> Other <br>
                    </td>
                </tr>
                
                <tr>
                    <td>PASSWORD: </td>
                    <td><input type="password" name="password" id="password" value="" placeholder="ENTER PASSWORD"></td>
                </tr>
                <tr>
                    <td>CONFIRM PASSWORD:</td>
                    <td><input type="password" name="confirmpassword" id="confirmpassword" value="" placeholder="CONFIRM PASSWORD"></td>
                </tr>


                <tr>
                    <td>ADDRESS:</td>
                    <td>
                    <textarea name="address" rows="4" cols="30" placeholder="Enter your address"></textarea>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <input type="submit" name="submit" value="SIGN UP">
            
                    </td>
                </tr>

                <tr>
                    
                    <td></td>
                    <td>
                        Already Have an Account?
                        <a href="login.php"> Sign In</a>
                    </td>
                </tr>
            </table>
            </fieldset>
        </form>
</body>
</html>
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Signup Page</title>
    <link rel="stylesheet" href="../assets/style_signup.css">
</head>
<body>

<div class="signup-container">
    <form class="signup-form" action="../controllers/signupCheck.php" method="post">
        <fieldset>
            <legend>Signup Page</legend>

            <table>
                <tr>
                    <td>FULL NAME:</td>
                    <td><input type="text" name="fullname" placeholder="ENTER FULL NAME"></td>
                </tr>

                <tr>
                    <td>USERNAME:</td>
                    <td><input type="text" name="username" placeholder="ENTER UNIQUE USERNAME"></td>
                </tr>

                <tr>
                    <td>EMAIL:</td>
                    <td><input type="email" name="email" placeholder="example@email.com"></td>
                </tr>

                <tr>
                    <td>DATE OF BIRTH:</td>
                    <td><input type="date" name="dob"></td>
                </tr>

                <tr>
                    <td>GENDER:</td>
                    <td>
                        <input type="radio" name="gender" value="male"> Male <br>
                        <input type="radio" name="gender" value="female"> Female <br>
                        <input type="radio" name="gender" value="other"> Other
                    </td>
                </tr>

                <tr>
                    <td>PASSWORD:</td>
                    <td><input type="password" name="password" id="password" placeholder="ENTER PASSWORD"></td>
                </tr>

                <tr>
                    <td>CONFIRM PASSWORD:</td>
                    <td><input type="password" name="confirmpassword" id="confirmpassword" placeholder="CONFIRM PASSWORD"></td>
                </tr>

                <tr>
                    <td>ADDRESS:</td>
                    <td><textarea name="address" rows="4" cols="30" placeholder="Enter your address"></textarea></td>
                </tr>

                <tr>
                    <td></td>
                    <td><input type="submit" value="SIGN UP"></td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        Already Have an Account?
                        <a href="login.php">Sign In</a>
                    </td>
>>>>>>> 398e55f6f2dbf4b37aaf57e9117711dbcccecfcd
                </tr>
            </table>
        </fieldset>
    </form>
</div>

<<<<<<< HEAD
<script src="../assets/signupValidate.js"></script>
<script src="../assets/showpassword.js"></script>

=======
>>>>>>> 398e55f6f2dbf4b37aaf57e9117711dbcccecfcd
</body>
</html>
