<?php
/*I certify that this submission is my own original work. Your name and RAM ID:  ___Mitchell Lam(R02050124)___ 
  Documentation:

    User Authentication Check:
        Redirects the user to the login page (login.php) if the session variable $_SESSION["username"] is not set.
        Terminate script execution using exit after redirection.

    Search Form:
        Displays a form for searching records with dropdown select options for choosing a search field (name, description, category, price, quantity) and an input field for entering the search term.
        Submits the form data to the current PHP script ($_SERVER["PHP_SELF"]) for processing.
    
    Search Record Processing:
        Processes form submissions using the HTTP POST method.
        Retrieves form data ($_POST['field'] and $_POST['search_term']) for search field and search term, respectively.
        Establishes a database connection using the credentials from config.php.
        Prepares and executes an SQL query to search for records matching the specified field and search term.
        Outputs the search results in an HTML table format if records are found, else displays a message indicating no results found.
        Closes the database connection and prepared statement after executing the search operation.
    
    Navigation Link:
        Provides a hyperlink to navigate back to the main menu page (main_menu.php).

*/
session_start();


if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Records</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Search Records</h1>
    <form class="css_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <span>Choose a field to search:
        <select name="field" id="field">
            <option value="name">Name</option>
            <option value="description">Description</option>
            <option value="category">Category</option>
            <option value="price">Price</option>
            <option value="quantity">Quantity</option>
        </select></span><br>
        <span>Enter search term: <input type="text" name="search_term"></span><br>
        <input class="submitBtn" type="submit" value="Search">
    </form>

    <?php

    require_once 'config.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['field']) && isset($_POST['search_term'])) {

      $search_field = $_POST['field'];
      $search_term = $_POST['search_term'];

      $search_field = htmlspecialchars($search_field);
      $search_term = htmlspecialchars($search_term);

      $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM products WHERE $search_field LIKE ?";
        $stmt = $conn->prepare($sql);
        $search_term = '%' . $search_term . '%';
        $stmt->bind_param("s", $search_term);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            echo "<h2>Search Results:</h2>";
            echo "<table><tr><th>ID</th><th>Name</th><th>Description</th><th>Category</th><th>Price</th><th>Quantity</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>".$row["product_id"]."</td><td>".$row["name"]."</td><td>".$row["description"]."</td><td>".$row["category"]."</td><td>".$row["price"]."</td><td>".$row["quantity"]."</td></tr>";
            }
            echo "</table>";
        } else {
            echo "No results found";
        }

        $stmt->close();
        $conn->close();
    }
    ?>
    <br><br>
    <a href="main_menu.php">Back to Main Menu</a>
</body>
</html>
