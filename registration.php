<?php
/*I certify that this submission is my own original work. Your name and RAM ID:  ___Mitchell Lam(R02050124)___ 
  Documentation:

    Client-Side Validation Functions:

        validateForm(): Performs comprehensive form validation including email format, field completion, username length, password complexity and Compares the password and confirm password fields to ensure they match..

        The showError function is responsible for displaying error messages on the user interface in response to validation errors. It utilizes dynamic HTML manipulation to create a new span element containing the error message and appends it to a designated error container within the HTML document.

    PHP Functions:
        sanitizeInput($input): Sanitizes input data by removing HTML tags and extraneous whitespace.
        validateEmail($email): Validates email format using PHP's filter_var function.
        validateUsername($username): Checks if the username length is at least 6 characters.
        validatePassword($password): Validates password complexity using a regular expression. atleast 1 number, atleast 1 upper, atleast 1 lower and 8 minimum length
        validatePasswordsMatch($password, $confirmPassword): Checks if the password and confirm password fields match.
        handleValidateRegistrationInput($email, $username, $password, $confirmPassword): Validates registration input data and returns an array of errors.
    Form Submission Handling:
        Processes form submissions using the HTTP POST method.
        Sanitizes and validates input data.
        Checks for input errors and displays error messages if validation fails.
        Inserts the user into the database if validation succeeds, after ensuring the username is not already taken.
    Database Interaction:
        Uses the config.php file to establish a connection to the database.
        Checks if the entered username already exists in the database before registration.
        Hashes the password using PHP's password_hash function before inserting it into the database.
    Form for User Registration:
        Displays an HTML form with input fields for email, username, password, and confirm password.
        Submits the form data to the current PHP script ($_SERVER["PHP_SELF"]) for processing.
        Utilizes JavaScript for client-side password validation to provide immediate feedback to the user.
    Navigation Links:
        Provides a hyperlink to navigate to the login page (login.php) for users who already have an account.
*/
?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="styles.css">
    <script>
         let errorContainer;

        function showError(errorMessage) {
           
            errorContainer = document.getElementById("errorContainer");
            let errorSpan = document.createElement("span");
            errorSpan.textContent = errorMessage;
            errorContainer.appendChild(errorSpan);
        }

        function validateForm(form) {

            let email = form.email.value;
            let username = form.username.value;
            let password = form.password.value;
            let confirmPass = form.confirm_password.value;
            errorContainer = document.getElementById("errorContainer");
            errorContainer.innerHTML = '';

            if (email === "" || username === "" || password === "" || confirmPass === "") {
                showError("Client Error: Please fill in all fields.");
                return false;
            }

            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError("Client Error: Invalid email format.");
                return false;
            }

            console.log("Current username length: ",username.length);
            if (username.length < 6) {
                showError("Client Error: Username must be at least 6 characters long.");
                return false;
            }

            let passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
            if (!passwordRegex.test(password)) {
                showError("Client Error: Password must contain at least 8 characters including at least one number, one uppercase and one lowercase letter.");
                return false;
            }

            if (password !== confirmPass) {
                showError("Client Error: Passwords do not match.");
                return false;
            }

            return true;
        }
    </script>
</head>

<body>
    <h1>User Registration</h1>

    <?php
    require_once 'config.php';

    function sanitizeInput($input)
    {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function validateUsername($username)
    {
        return strlen($username) >= 6;
    }

    function validatePassword($password)
    {
        $passwordRegex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/';
        return preg_match($passwordRegex, $password);
    }

    function validatePasswordsMatch($password, $confirmPassword)
    {
        return $password === $confirmPassword;
    }

    function handleValidateRegistrationInput($email, $username, $password, $confirmPassword)
    {
        $errors = [];

        if (!validateEmail($email)) {
            $errors[] = "Invalid email format.";
        }

        if (!validateUsername($username)) {
            $errors[] = "Username must be at least 6 characters long.";
        }

        if (!validatePassword($password)) {
            $errors[] = "Password must contain at least 8 characters including at least one number, one uppercase and one lowercase letter.";
        }

        if (!validatePasswordsMatch($password, $confirmPassword)) {
            $errors[] = "Passwords do not match.";
        }

        return $errors;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = sanitizeInput($_POST["email"]);
        $username = sanitizeInput($_POST["username"]);
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];

        $inputErrors = handleValidateRegistrationInput($email, $username, $password, $confirm_password);

        if (!empty($inputErrors)) {
            foreach ($inputErrors as $error) {
                echo "<span>PHP Validation Error: $error</span><br>";
            }
        } else {

            $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);
            $query_check_username = "SELECT * FROM users WHERE username = '$username'";
            $result_check_username = $conn->query($query_check_username);
            if ($result_check_username->num_rows > 0) {
                echo "<span>Error: Username already taken. <a href='registration.php'>Try another username</a></span>";
            } else {

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $query_insert_user = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";
                $result_insert_user = $conn->query($query_insert_user);
                if (!$result_insert_user) {
                    echo "Error: " . $conn->error;
                } else {
                    echo "<span>User registered successfully. You can now <a href='login.php'>login</a>.</span>";
                }
            }

            $conn->close();
        }
    }
    ?>

    <form class="css_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST"
        onsubmit="return validateForm(this)">
        <span>Email: <input type="email" name="email" required></span><br>
        <span>Username: <input type="text" name="username" required></span><br>
        <span>Password: <input type="password" name="password" required></span><br>
        <span>Confirm Password: <input type="password" name="confirm_password" required></span><br>
        <input class="submitBtn" type="submit" value="Register">
    </form>

    <div id="errorContainer"></div>

    <br><span>Already have an account? <a href="login.php">Log in here</a></span>
</body>

</html>