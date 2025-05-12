<?php
$conn = new mysqli("localhost", "root", "", "smart_restaurant");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle completed toggle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['table_number'])) {
    $table_number = $_POST['table_number'];
    $completed = $_POST['completed'] === '1' ? 1 : 0;
    $conn->query("UPDATE orders SET completed = $completed WHERE table_number = '$table_number'");
}

// Get grouped data
$sql = "
    SELECT 
        table_number, 
        GROUP_CONCAT(CONCAT('Item: ', item_number, ' (Qty: ', quantity, ')') SEPARATOR ', ') AS items,
        MIN(order_time) AS order_time,
        MAX(completed) AS completed
    FROM orders
    GROUP BY table_number
    ORDER BY order_time DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>G6 Service Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="text-center mb-4">Service Provider Dashboard</h1>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Table Number</th>
                <th>Ordered Items</th>
                <th>Order Time</th>
                <th>Completed</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $count = 1;
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $count++ . "</td>";
                    echo "<td>" . htmlspecialchars($row["table_number"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["items"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["order_time"]) . "</td>";
                    echo "<td>";
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='table_number' value='" . $row["table_number"] . "'>";
                    echo "<input type='hidden' name='completed' value='" . ($row["completed"] ? 0 : 1) . "'>";
                    echo "<button type='submit' class='btn btn-sm " . ($row["completed"] ? "btn-success" : "btn-outline-secondary") . "'>";
                    echo $row["completed"] ? "✓ Completed" : "Mark Done";
                    echo "</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<footer class="text-center mt-5">
    <p>&copy; 2025 G6 SMART Restaurant. All Rights Reserved.</p>
</footer>
</body>
</html>

<?php $conn->close(); ?>
