<?php
$servername = "localhost";
$username = "root";  // change if different
$password = "";      // change if different
$dbname = "smart_restaurant";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data
$table_number = $_POST['menu'];
$itemNumbers = preg_split("/[\s,]+/", $_POST['itemNumbers']);

foreach ($itemNumbers as $item) {
    $item = trim($item);
    if ($item === "") continue;

    $quantityField = 'quantity_' . $item;
    $quantity = isset($_POST[$quantityField]) ? (int)$_POST[$quantityField] : 0;

    if ($quantity > 0) {
        $stmt = $conn->prepare("INSERT INTO orders (table_number, item_number, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $table_number, $item, $quantity);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();

echo "Order submitted successfully!";
?>
