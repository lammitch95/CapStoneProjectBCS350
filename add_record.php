<?php

/*I certify that this submission is my own original work. Your name and RAM ID:  ___Mitchell Lam(R02050124)___ 
  Documentation:

  -  resumes the existing session. This is necessary to utilize session variables throughout the script.
  - Checks if the user is not logged in by verifying if the session variable $_SESSION["username"] is not set
  - If the user is not logged in, redirects the user to the login page (login.php) using the header() function and terminates script execution using exit.

  validateForms():
    Performs client-side validation of the form inputs before submission.
    form (HTMLFormElement): The form element to validate.
    return true if all inputs are valid, otherwise false.
    Retrieves the values of the price and quantity inputs.
    Uses regular expressions to validate the price and quantity formats.
    If any input fails validation, an error message is displayed using the showError function, and the function returns false.

  PHP Function generateProductCode:
        - Generates a unique product code based on the provided name and category.
        - Uses the first three characters of the name and category, combined with a random three-digit number.
        - Checks if the generated product code already exists in the database. If it does, recursively calls - itself to generate a new code until a unique one is found.
  
 Form Submission Handling:
       - Processes form submissions using the HTTP POST method.
        -Retrieves form data ($_POST) including name, description, category, price, and quantity.
        -Establishes a database connection using the credentials from config.php.
        -Calls the generateProductCode function to generate a unique product code.
       - Prepares an SQL statement to insert the new record into the products table.
        -Binds the form data to the SQL statement parameters.
        -Executes the SQL statement to insert the new record into the database.
        -Outputs success or error messages based on the result of the insertion operation.
Form for Adding Record:
    -Displays an HTML form with input fields for entering name, description, category, price, and quantity.
    -Submits the form data to the current PHP script ($_SERVER["PHP_SELF"]) for processing.
    -Uses the htmlspecialchars function to prevent XSS (Cross-Site Scripting) attacks by escaping special characters in the form action attribute.


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
    <title>Add Record</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        let errorContainer;
         
        function showError(errorMessage) {
           
           let errorSpan = document.createElement("span");
           errorSpan.textContent = errorMessage;
           errorContainer.appendChild(errorSpan);
         }

         function validateForm(form) {
            errorContainer = document.getElementById("errorContainer");
            errorContainer.innerHTML = ''; 
           
            let price = form.price.value;
            let quantity = form.quantity.value;

            let priceRegex = /^\d+(\.\d{1,2})?$/;
            if (!priceRegex.test(price)) {
                showError("Price must be a decimal number with up to two decimal places.");
                return false;
            }
            
            let quantityRegex = /^[1-9]\d*$/;
            if (!quantityRegex.test(quantity)) {
                showError("Quantity must be a positive integer value.");
                return false;
            }

            return true; 
        }

        
    </script>
</head>
<body>
    <h1>Add Record</h1>
    <?php

    require_once 'config.php';

    function generateProductCode($name, $category, $conn){

        $nameInitial = substr($name, 0, 3); 
        $categoryInitial = substr($category, 0, 3); 
        $product_id = strtoupper($nameInitial . $categoryInitial . rand(100, 999)); 
        
        $sql_check = "SELECT product_id FROM products WHERE product_id = '$product_id'";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->execute();
        $stmt_check->store_result();
        $num_rows = $stmt_check->num_rows;
        $stmt_check->close();

        if ($num_rows > 0) {
            return generateProductCode($name, $category, $conn); 
        } else {
            return  $product_id;
        }


    }
   
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
     
        $name = $_POST["name"];
        $description = $_POST["description"];
        $category = $_POST["category"];
        $price = $_POST["price"];
        $quantity = $_POST["quantity"];

         
        $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $product_id = generateProductCode($name, $category, $conn);

        $sql = "INSERT INTO products (product_id, name, description, category, price, quantity) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
          die("Error in preparing statement: " . $conn->error);
        }
        $stmt->bind_param("sssssd", $product_id, $name, $description, $category, $price, $quantity);

        if ($stmt->execute()) {
            echo "Record added successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
        $conn->close();
    }
    ?>
    <form class="css_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"  method="POST" onsubmit="return validateForm(this)">
        <span>Name: <input type="text" name="name"></span><br>
       <span>Description: <input type="text" name="description"></span><br>
        <span>Category: <input type="text" name="category"></span><br>
       <span>Price: <input type="text" name="price"></span><br>
        <span>Quantity: <input type="text" name="quantity"></span><br>
        <input class="submitBtn" type="submit" value="Add Record">
    </form>

    <div id="errorContainer"></div>
    <br><br>
    <a href="main_menu.php">Back to Main Menu</a>
</body>
</html>
