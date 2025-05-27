<?php

/*I certify that this submission is my own original work. Your name and RAM ID:  ___Mitchell Lam(R02050124)___ 
  Documentation:

  -  resumes the existing session. This is necessary to utilize session variables throughout the script.
  - Checks if the user is not logged in by verifying if the session variable $_SESSION["username"] 
  - If the user is not logged in, redirects the user to the login page (login.php) using the header() function and terminates script execution using exit.


Logout Check:
    Checks if the "logout" parameter is set in the GET request ($_GET).
    If set, clears the session data ($_SESSION), destroys the session, and redirects the user to the login page (login.php).
    Terminates script execution using exit after redirecting.

Welcome Message:
    Displays a welcome message to the user, including their username retrieved from the session ($_SESSION["username"]).

Navigation Links:
        Provides navigation links to various functionalities:
            "List Records": Links to the page for listing records (list_records.php).
            "Add Record": Links to the page for adding a new record (add_record.php).
            "Search Records": Links to the page for searching records (search_record.php).
            "Delete Record": Links to the page for deleting a record (delete_record.php).
            
Logout Link:
    Provides a link to logout by appending "?logout=true" to the URL.
    Clicking on the logout link triggers the logout functionality described above.

  */
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET["logout"])) {
    $_SESSION = array();
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Menu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Main Menu</h1>
    <?php include 'setupDB.php'; ?>
    <div id="welcome-message">
        <?php echo "Welcome " . $_SESSION["username"] . "!"; ?>
    </div>
    <ul>
        <li><a href="list_records.php">List Records</a></li>
        <li><a href="add_record.php">Add Record</a></li>
        <li><a href="search_record.php">Search Records</a></li>
        <li><a href="delete_record.php">Delete Record</a></li>
    </ul>
    <br><span>Click here to <a href="?logout=true">Logout</span></a>
</body>
</html>
