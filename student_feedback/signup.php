<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "signup";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected to database!<br>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "POST request received!<br>";

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO signup(name, email, phone, password) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssss", $name, $email, $phone, $password);

    if ($stmt->execute()) {
        echo "Signup successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
