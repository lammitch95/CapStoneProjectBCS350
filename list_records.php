<?php

/*I certify that this submission is my own original work. Your name and RAM ID:  ___Mitchell Lam(R02050124)___ 
  Documentation:

  -  resumes the existing session. This is necessary to utilize session variables throughout the script.
  - Checks if the user is not logged in by verifying if the session variable $_SESSION["username"] is not set
  - If the user is not logged in, redirects the user to the login page (login.php) using the header() function and terminates script execution using exit.

  PHP Database Query:
        -Requires inclusion of the config.php file for database configuration.
        -Establishes a database connection using provided credentials ($dbservername, $dbusername, $dbpassword, $dbname).
       -Checks for successful connection and terminates script with an error message if connection fails.
        -Executes an SQL query to select all records from the products table.
        -Retrieves the query result.
  Displaying Records:
        -Checks if there are any records returned from the query and if the result object exists.
        -If records are found, constructs an HTML table to display records, including columns for ID, Name, -Description, Category, Price, and Quantity.
        -Iterates through each record fetched from the database result and outputs corresponding table rows with record details.
       - Closes the HTML table.
    No Results Handling:
        -If no records are found in the products table, outputs a message indicating "0 results".

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
    <title>List Records</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>List Records</h1>
    <?php
       
       require_once 'config.php';


      $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);


      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      $sql = "SELECT * FROM products";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
          echo "<table>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                  </tr>";

          while($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>".$row["product_id"]."</td>
                      <td>".$row["name"]."</td>
                      <td>".$row["description"]."</td>
                      <td>".$row["category"]."</td>
                      <td>".$row["price"]."</td>
                      <td>".$row["quantity"]."</td>
                    </tr>";
          }
          echo "</table>";
      } else {
          echo "0 results from products table.";
      }

      $conn->close();
  ?>
  <br><br>
 <a href="main_menu.php">Back to Main Menu</a>
</body>
</html>
