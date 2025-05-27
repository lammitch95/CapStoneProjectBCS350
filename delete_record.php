<?php
/*I certify that this submission is my own original work. Your name and RAM ID:  ___Mitchell Lam(R02050124)___ 
  Documentation:
  -  resumes the existing session. This is necessary to utilize session variables throughout the script.
  - Checks if the user is not logged in by verifying if the session variable $_SESSION["username"] is not set
  - If the user is not logged in, redirects the user to the login page (login.php) using the header() function and terminates script execution using exit.
  
  Record Deletion:

    - Processes form submissions using the HTTP POST method.
    - Retrieves the product_id from the submitted form data and prepares an SQL statement to delete the corresponding record from the products table.
    - Executes the prepared statement to delete the record.
    - Outputs success or error messages based on the result of the deletion operation.

  Record Display:
        - Retrieves all records from the products table using a SQL SELECT query.
        - Checks if there are results returned from the query.
        - If records are found, constructs an HTML table to display the records, including columns for ID, Name, - Description, Category, Price, Quantity, and an option to delete each record.
        - Provides a form for each record with a hidden input field containing the product_id and a delete button.
        - If no records are found, displays a message indicating "0 results".
*/
session_start();

// Check if user is not logged in, redirect to login page
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
    <title>List Records</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Delete Record</h1>
      <?php
      require_once 'config.php';

      $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);


      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST['product_id'];
    
        $stmt = $conn->prepare("DELETE FROM products WHERE product_id= ?");
        $stmt->bind_param("s", $id);
    
        if ($stmt->execute() === TRUE) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    
        $stmt->close();
    }
    
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<table><tr><th>ID</th><th>Name</th><th>Description</th><th>Category</th><th>Price</th><th>Quantity</th><th>Option</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["product_id"]."</td><td>".$row["name"]."</td><td>".$row["description"]."</td><td>".$row["category"]."</td><td>".$row["price"]."</td><td>".$row["quantity"]."</td><td><form method='POST' action='".$_SERVER["PHP_SELF"]."'><input type='hidden' name='product_id' value='".$row["product_id"]."'><input class='submitBtn' type='submit' value='Delete'></form></td></tr>";
        }
        echo "</table>";
    } else {
        echo " 0 results";
    }
    
    $conn->close();
      ?>
  <br><br>
 <a href="main_menu.php">Back to Main Menu</a>
</body>
</html>
