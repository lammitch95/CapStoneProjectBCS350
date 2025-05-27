<?php

/*I certify that this submission is my own original work. Your name and RAM ID:  ___Mitchell Lam(R02050124)___ 
  Documentation:

  -  resumes the existing session. This is necessary to utilize session variables throughout the script.
  - Checks if the user is logged in by verifying if the session variable $_SESSION["username"] 
  - If the user is  logged in, redirects the user to the main menu page (main_menu.php) using the header() function and terminates script execution using exit.

  Function to Sanitize User Input:
        - sanitizeInput($input): Function to sanitize user input by removing HTML tags, trimming whitespace, and - converting special characters to HTML entities.
        - Returns sanitized input.
        - Form Submission Handling:
        - Checks if the form is submitted using the POST method ($_SERVER["REQUEST_METHOD"] == "POST").
        - Retrieves username and password from the form data ($_POST).
        - Establishes a database connection using credentials from config.php.
        - Prepares an SQL statement to select user data based on the provided username.
        - Executes the SQL statement and retrieves the result set.
        - If a user with the provided username is found:
        - Fetches the user data from the result set.
        - Verifies the password using the password_verify function.
  If the password is correct:
        - Sets the $_SESSION["username"] variable to the username.
        - Redirects the user to the main menu page (main_menu.php).
        - Exits the script.
        - If the password is incorrect:
        - Sets an error message indicating incorrect password.
        - If the username is not found:
        - Sets an error message indicating user not found.
    Form for User Login:
        - Displays an HTML form for user login with input fields for username and password.
        - Submits the form data to the current PHP script ($_SERVER["PHP_SELF"]) for processing.
        - sanatize input using  the htmlspecialchars function.
   Navigation Link:
        - Provides a hyperlink to navigate to the registration page (registration.php) for user registration if the user doesn't have an account.


  */
session_start();

require_once 'config.php';

// Check if user is already logged in, redirect to main menu if so
if(isset($_SESSION["username"])) {
    header("Location: main_menu.php");
    exit;
}

// Function to sanitize user input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST["username"]);
    $password = $_POST["password"];

    
    $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);
    if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error);}
   
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            
            $_SESSION["username"] = $username;
            header("Location: main_menu.php");
            exit;
        } else {
            
            $error_message = "<span>Error: Incorrect password. <a href='login.php'>Try again</a></span>";
        }
    } else {
        
        $error_message = "<span>Error: User not found. <a href='login.php'>Try again</a></span>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>User Login</h1>

    <?php
    if (isset($error_message)) {
        echo $error_message;
    }
    ?>

    <form class="css_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <span>Username: <input class="input_fields" type="text" name="username" required></span><br>
        <span>Password: <input class="input_fields" type="password" name="password" required></span><br>
        <input class="submitBtn" type="submit" value="Login">
    </form>

    <br><span>Don't have an account? <a href="registration.php">Register here</a></span>
</body>
</html>
