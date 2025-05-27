
  <?php

/*I certify that this submission is my own original work. Your name and RAM ID:  ___Mitchell Lam(R02050124)___ 
  Documentation:

  - establishes a connection to the MySQL database server using the provided credentials ($dbservername, $dbusername, $dbpassword, $dbname).

  - checks if the users table exists in the database. If not, it creates the table with the following schema:
        username (VARCHAR(50)): Primary key for identifying users.
        email (VARCHAR(255)): Stores the email addresses of users.
        password (VARCHAR(255)): Stores hashed passwords of users.

  - checks if the products table exists in the database. If not, it creates the table with the following schema:
        product_id (VARCHAR(50)): Primary key for identifying products.
        name (VARCHAR(255)): Stores the names of products.
        description (TEXT): Stores the descriptions of products.
        category (VARCHAR(50)): Stores the categories of products.
        price (DECIMAL(10, 2)): Stores the prices of products with two decimal places.
        quantity (INT): Stores the quantities of products.

  -  closes the database connection to free up resources
  - If any of the database operations fail (e.g., table creation), the script terminates execution and displays an error message indicating the failure.
  - ensures the existence of a products table in the database and populates it with initial data if it's empty. It first attempts to create the table and then checks if it's empty. If empty, it inserts predefined data into the table.

 */
    require_once 'config.php';


    $conn = new mysqli($dbservername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) die("Fatal Error");

    /*$query_drop = "DROP TABLE IF EXISTS users";
    $result_drop = $conn->query($query_drop);
    if (!$result_drop) die ("Failed to drop old users table");*/

    $query_users = "CREATE TABLE IF NOT EXISTS users (
      username VARCHAR(50) PRIMARY KEY,
      email VARCHAR(255),
      password VARCHAR(255)
    )";

    $result_create_users = $conn->query($query_users);
    if (!$result_create_users) die ("Failed to create users table");

    $query_products = "CREATE TABLE IF NOT EXISTS products (
        product_id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(255),
        description TEXT,
        category VARCHAR(50),
        price DECIMAL(10, 2),
        quantity INT
    )";



    $result_products = $conn->query($query_products);
    if (!$result_products) die ("Failed to create products table");

    $query_check_empty = "SELECT COUNT(*) as count FROM products";
    $result_check_empty = $conn->query($query_check_empty);
    $row = $result_check_empty->fetch_assoc();
    $product_count = $row['count'];

    if ($product_count == 0) {

        $query = "INSERT IGNORE INTO products (product_id, name, description, category, price, quantity) VALUES
      ('smx001','Smartphone X', 'High-end smartphone with advanced features', 'Electronics', 699.99, 100),
      ('lapy001','Laptop Y', 'Powerful laptop for productivity and gaming', 'Electronics', 1299.99, 50)";

      
      $result = $conn->query($query);
      if (!$result) die ("Database access failed");
    }
    

    $conn->close();
  ?>
