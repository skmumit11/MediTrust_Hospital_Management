
<?php
session_start();

$old = [
    'fullname' => '',
    'username' => '',
    'email'    => '',
    'dob'      => '',
    'gender'   => '',
    'address'  => ''
];

if(isset($_SESSION['old']) && is_array($_SESSION['old'])){
    $old = array_merge($old, $_SESSION['old']);
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
        
        <!-- Display errors -->
        
 <fieldset>
            <legend>Sign Up</legend>

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
                    <td>FULL NAME:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="text" name="fullname" value="<?php echo htmlspecialchars($old['fullname']); ?>" placeholder="Enter Full Name">
                        <span id="fullnameError" class="error-msg"></span>
                    </td>
                </tr>

                <tr>
                    <td>USERNAME:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="text" name="username" value="<?php echo htmlspecialchars($old['username']); ?>" placeholder="Enter Username">
                        <span id="usernameError" class="error-msg"></span>
                    </td>
                </tr>

                <tr>
                    <td>EMAIL:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="text" name="email" value="<?php echo htmlspecialchars($old['email']); ?>" placeholder="Enter Email">
                        <span id="emailError" class="error-msg"></span>
                    </td>
                </tr>

                <tr>
                    <td>DATE OF BIRTH:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="date" name="dob" value="<?php echo htmlspecialchars($old['dob']); ?>">
                        <span id="dobError" class="error-msg"></span>
                    </td>
                </tr>

                <tr>
                    <td>GENDER:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <select name="gender">
                            <option value="">-- Select --</option>
                            <option value="Male"   <?php echo ($old['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($old['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other"  <?php echo ($old['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <span id="genderError" class="error-msg"></span>
                    </td>
                </tr>

                <tr>
                    <td>ADDRESS:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="text" name="address" value="<?php echo htmlspecialchars($old['address']); ?>" placeholder="Enter Address">
                        <span id="addressError" class="error-msg"></span>
                    </td>
                </tr>

                <tr>
                    <td>PASSWORD:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="password" name="password" placeholder="Enter Password">
                        <span id="passwordError" class="error-msg"></span>
                    </td>
                </tr>

                <tr>
                    <td>CONFIRM PASSWORD:</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="password" name="confirmpassword" placeholder="Confirm Password">
                        <span id="confirmpasswordError" class="error-msg"></span>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <input type="submit" class="btn" name="submit" value="Sign Up">
                    </td>
                </tr>

                <tr>
                    <td>Already have an account?</td>
                    <td><a href="login.php">Log In</a></td>
                </tr>
            </table>
        </fieldset>
    </form>

</div>



<script src="../assets/signupValidation.js"></script>
<script src="../assets/showpassword.js"></script>

</body>
</html>

<?php unset($_SESSION['old']); ?>
